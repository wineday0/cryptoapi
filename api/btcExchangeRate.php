<?php

namespace Main\BTCExchange;

class btcExchangeRate
{
    protected $source = 'https://blockchain.info/ticker';
    protected $supplement =  1.02;
    private $rates = [];
    private function checkRes()
    {
        $res =  is_null($this->rates) || count($this->rates) < 1 ?  json_encode(['status' => "error", 'code' => 404, 'message' => "Given Symbol Not Found"]) : json_encode(['status' => "success", 'code' => 200, 'data' => $this->rates]);
        return $res;
    }
    private function getData()
    {
        $rawData = file_get_contents($this->source);
        $decodedData = json_decode($rawData);
        return $decodedData;
    }
    private function rates($exc = 'all')
    {
        $getData = $this->getData();
        foreach ($getData as $rate) {
            if ($exc == 'all') {
                $this->rates += [$rate->symbol => round($rate->buy * $this->supplement, 9)];
            } else {
                if ($rate->symbol == $exc) {
                    $this->rates += [$rate->symbol => round($rate->buy * $this->supplement, 9)];
                }
            }
        }
        asort($this->rates);
        return $this->checkRes();
    }
    private function convert($from, $to, $value)
    {

        $getData = $this->getData();
        foreach ($getData as $rate) {
            if ($from != "BTC") {
                if ($rate->symbol == $from) {
                    $this->rates += ['currency_from' => $from, 'currency_to' => $to, 'value' => $value, 'converted_value' => round((float)$value / $rate->buy * $this->supplement, 9), 'rate' => round($rate->buy * $this->supplement, 9)];
                }
            } else {
                if ($rate->symbol == $to) {
                    $this->rates += ['currency_from' => $from, 'currency_to' => $to, 'value' => $value, 'converted_value' => round($rate->buy * (float)$value * $this->supplement, 2), 'rate' => round($rate->buy * $this->supplement, 9)];
                }
            }
        }
        return $this->checkRes();
    }
    public function getRates($exc = 'all')
    {
        return $this->rates($exc);
    }
    public function getConvert($from, $to, $value)
    {
        return $this->convert($from, $to, $value);
    }
}
