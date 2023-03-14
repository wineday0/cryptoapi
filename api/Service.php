<?php

namespace Main;

/**
 * Class Service
 */
class Service
{
    public const EXCHANGE_RATE_ALL = 'all';
    public const BTC_CURRENCY_CODE = 'BTC';
    public const FEE = 1.02;
    private const ENDPOINT = 'https://blockchain.info/ticker';

    private array $rates = [];

    /**
     * @return false|string
     */
    private function getResponse(): bool|string
    {
        $response = new Response();
        $response->data = $this->rates;
        return empty($this->rates)
            ? $response->getResponseSymbolNotFound()
            : $response->getResponseSuccess();
    }

    /**
     * @return mixed
     */
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
     * @param $currencyFrom
     * @param $currencyTo
     * @param $value
     * @return bool|string
     */
    private function convert($currencyFrom, $currencyTo, $value)
    {
        $getData = $this->getData();
        foreach ($getData as $rate) {
            if ($currencyFrom != static::BTC_CURRENCY_CODE) {
                if ($rate->symbol == $currencyFrom) {
                    $this->rates += [
                        'currency_from' => $currencyFrom,
                        'currency_to' => $currencyTo,
                        'value' => $value,
                        'converted_value' => round((float)$value / $rate->buy * static::FEE, 9),
                        'rate' => $this->getPreparedPurchasePrice($rate->buy)
                    ];
                }
            } else {
                if ($rate->symbol == $currencyTo) {
                    $this->rates += [
                        'currency_from' => $currencyFrom,
                        'currency_to' => $currencyTo,
                        'value' => $value,
                        'converted_value' => round((float)$value * $rate->buy * static::FEE, 2),
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
