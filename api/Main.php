<?php

namespace Main;

use Main\BTCExchange\BtcExchangeRate;
use Main\Token\CheckToken;

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
        $token = new CheckToken();
        $response = new Response();
        $invToken = $response->getResponseInvalidToken();

        if (!$token->checkToken()) {
            return $invToken;
        }

        $invMethod = $response->getResponseMethodNotFound();
        $invValue = $response->getResponseInvalidValue();
        $rate = new BtcExchangeRate();

        switch ($_GET['method']) {
            case static::METHOD_RATES:
                $currency = $this->getPreparedCurrency($_GET['currency']);
                return $rate->getRates($currency);
            case static::METHOD_CONVERT:
                $requestedValue = (float)$_GET['value'];
                if ($requestedValue < 0.01) {
                    return $invValue;
                }
                return $rate->getConvert(
                    strtoupper($_GET['currency_from']),
                    strtoupper($_GET['currency_to']),
                    $requestedValue
                );
            default:
                return $invMethod;
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
            || $currency == BtcExchangeRate::EXCHANGE_RATE_ALL
            || strlen($currency) < 3
        ) {
            return BtcExchangeRate::EXCHANGE_RATE_ALL;
        }
        return strtoupper($currency);
    }
}
