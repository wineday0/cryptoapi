<?php

namespace Main\BTCExchange;

use Main\Response;

/**
 * Class BtcExchangeRate
 */
class BtcExchangeRate
{
    public const EXCHANGE_RATE_ALL = 'all';
    public const BTC_CURRENCY_CODE = 'BTC';
    public const FEE = 1.02;
    private const ENDPOINT = 'https://blockchain.info/ticker';
    private $rates = [];

    /**
     * @return false|string
     */
    private function getResponse(): bool|string
    {
        $response = new Response();
        $response->data = $this->rates;
        return (is_null($this->rates) || count($this->rates) < 1)
            ? $response->getResponseSymbolNotFound()
            : $response->getResponseSuccess();
    }

    private function getData()
    {
        $rawData = file_get_contents($this->getEndpoint());
        return json_decode($rawData);
    }

    /**
     * @param string $expectedCurrency
     * @return bool|string
     */
    private function rates(string $expectedCurrency)
    {
        $getData = $this->getData();

        foreach ($getData as $symbol => $rate) {
            if (!in_array($expectedCurrency, [$symbol, static::EXCHANGE_RATE_ALL])) {
                continue;
            }
            $this->rates += [$symbol => $this->getPreparedPurchasePrice($rate->buy)];
        }
        asort($this->rates);
        return $this->getResponse();
    }

    /**
     * @param $from
     * @param $to
     * @param $value
     * @return bool|string
     */
    private function convert($from, $to, $value)
    {
        $getData = $this->getData();
        foreach ($getData as $rate) {
            if ($from != static::BTC_CURRENCY_CODE) {
                if ($rate->symbol == $from) {
                    $this->rates += [
                        'currency_from' => $from,
                        'currency_to' => $to,
                        'value' => $value,
                        'converted_value' => round((float)$value / $rate->buy * static::FEE, 9),
                        'rate' => $this->getPreparedPurchasePrice($rate->buy)
                    ];
                }
            } else {
                if ($rate->symbol == $to) {
                    $this->rates += [
                        'currency_from' => $from,
                        'currency_to' => $to,
                        'value' => $value,
                        'converted_value' => round($rate->buy * (float)$value * static::FEE, 2),
                        'rate' => $this->getPreparedPurchasePrice($rate->buy)
                    ];
                }
            }
        }
        return $this->getResponse();
    }

    /**
     * @param string|null $expectedCurrency
     * @return bool|string
     */
    public function getRates(?string $expectedCurrency = self::EXCHANGE_RATE_ALL): bool|string
    {
        return $this->rates($expectedCurrency);
    }

    /**
     * @param $currencyFrom
     * @param $currencyTo
     * @param $value
     * @return bool|string
     */
    public function getConvert($currencyFrom, $currencyTo, $value)
    {
        return $this->convert($currencyFrom, $currencyTo, $value);
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return static::ENDPOINT;
    }

    /**
     * @param $purchasePrice
     * @return float
     */
    public function getPreparedPurchasePrice($purchasePrice): float
    {
        return round($purchasePrice * static::FEE, 9);
    }
}
