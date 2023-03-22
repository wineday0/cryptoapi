<?php

namespace Main;

/**
 * Class Main
 */
class Main
{
    public const METHOD_RATES = 'rates';
    public const METHOD_CONVERT = 'convert';

    public function __construct()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    }

    /**
     * @return bool|string
     */
    public function run()
    {
        $token = new Authorization();
        $response = new Response();

        if (!$token->validate()) {
            return $response->getResponseInvalidToken();
        }
        $rate = new Service();

        switch ($_GET['method']) {
            case static::METHOD_RATES:
                $currency = $this->getPreparedCurrency($_GET['currency']);
                return $rate->getRates($currency);
            case static::METHOD_CONVERT:
                $requestedValue = (float)$_GET['value'];
                if ($requestedValue < 0.01) {
                    return $response->getResponseInvalidValue();
                }
                return $rate->getConvert(
                    strtoupper($_GET['currency_from']),
                    strtoupper($_GET['currency_to']),
                    $requestedValue
                );
            default:
                return $response->getResponseMethodNotFound();
        }
    }

    /**
     * @param string|null $currency
     * @return string
     */
    private function getPreparedCurrency(?string $currency): string
    {
        $currency = trim($currency);
        if (
            empty($currency)
            || $currency == Service::EXCHANGE_RATE_ALL
            || strlen($currency) < 3
        ) {
            return Service::EXCHANGE_RATE_ALL;
        }
        return strtoupper($currency);
    }
}
