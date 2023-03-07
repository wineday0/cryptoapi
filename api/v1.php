<?php

namespace Main;

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

require_once 'CheckToken.php';
require_once 'BtcExchangeRate.php';

use Main\BTCExchange\BtcExchangeRate;
use Main\Token\CheckToken;

$token = new CheckToken();
$rate = new BtcExchangeRate();
$invToken = json_encode(['status' => "error", 'code' => 403, 'message' => "Invalid token"]);
$invValue = json_encode(['status' => "error", 'code' => 400, 'message' => "Invalid value"]);
$invMethod = json_encode(['status' => "error", 'code' => 404, 'message' => "Invalid Method"]);

if ($token->checkToken()) {
    if ($_GET['method'] == 'rates') {
        $currency = $_GET['currency'];
        $currency = is_null($currency) || strlen($currency) < 1 ? 'all' : strtoupper($_GET['currency']);
        print_r($rate->getRates($currency));
    } elseif ($_GET['method'] == 'convert') {
        if ((float)($_GET['value']) < 0.01) {
            print_r($invValue);
        } else {
            $value = (float)($_GET['value']);
            print_r(
                $rate->getConvert(
                    strtoupper($_GET['currency_from']),
                    strtoupper($_GET['currency_to']),
                    $value
                )
            );
        }
    } else {
        print_r($invMethod);
    }
} else {
    print_r($invToken);
}
