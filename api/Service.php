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

    /** @var array */
    private array $rates = [];

    /**
     * @param string|null $expectedCurrency
     * @return bool|string
     */
    public function getRates(?string $expectedCurrency = self::EXCHANGE_RATE_ALL): bool|string
    {
        $this->rates($expectedCurrency);
        return $this->getResponse();
    }

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param float $value
     * @return bool|string
     */
    public function getConvert(string $currencyFrom, string $currencyTo, float $value)
    {
        $this->convert($currencyFrom, $currencyTo, $value);
        return $this->getResponse();
    }

    /**
     * @param string $expectedCurrency
     * @return void
     */
    private function rates(string $expectedCurrency): void
    {
        $getData = $this->getData();

        foreach ($getData as $symbol => $rate) {
            if (!in_array($expectedCurrency, [$symbol, static::EXCHANGE_RATE_ALL])) {
                continue;
            }
            $this->rates += [$symbol => $this->getPreparedPurchasePrice($rate->buy)];
        }
        asort($this->rates);
    }

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param float $value
     * @return void
     */
    private function convert(string $currencyFrom, string $currencyTo, float $value): void
    {
        $getData = $this->getData();
        foreach ($getData as $rate) {
            if ($currencyFrom == static::BTC_CURRENCY_CODE
                && $rate->symbol == $currencyTo) {
                $this->rates += [
                    'currency_from' => $currencyFrom,
                    'currency_to' => $currencyTo,
                    'value' => $value,
                    'rate' => $this->getPreparedPurchasePrice($rate->buy),
                    'converted_value' => $this->calculateValueFromCurrency($currencyFrom, $value, $rate->buy),
                ];
                continue;
            }
            if ($currencyFrom == $rate->symbol) {
                $this->rates += [
                    'currency_from' => $currencyFrom,
                    'currency_to' => $currencyTo,
                    'value' => $value,
                    'rate' => $this->getPreparedPurchasePrice($rate->buy),
                    'converted_value' => $this->calculateValueFromCurrency($currencyFrom, $value, $rate->buy),
                ];
            }
        }
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return static::ENDPOINT;
    }

    /**
     * @param float $purchasePrice
     * @return float
     */
    public function getPreparedPurchasePrice(float $purchasePrice): float
    {
        return round($purchasePrice * static::FEE, 9);
    }

    /**
     * @param string $currency
     * @param float $value
     * @param float $rateBuy
     * @return float
     */
    private function calculateValueFromCurrency(string $currency, float $value, float $rateBuy): float
    {
        return match ($currency) {
            self::BTC_CURRENCY_CODE => round($value * $rateBuy * static::FEE, 2),
            default => round($value / $rateBuy * static::FEE, 9),
        };
    }

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
}
