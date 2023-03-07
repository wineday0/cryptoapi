<?php

namespace Main\BTCExchange;

/**
 * Class BtcExchangeRate
 */
class BtcExchangeRate
{
    public const BTC_CURRENCY_CODE = 'BTC';
    private const ENDPOINT = 'https://blockchain.info/ticker';

    protected $supplement = 1.02;
    private $rates = [];

    /**
     * @return false|string
     */
    private function checkRes(): bool|string
    {
        return
            is_null($this->rates)
            || count($this->rates) < 1
                ? $this->getResponseNotFound()
                : $this->getResponseSuccess();
    }

    private function getData()
    {
        $rawData = file_get_contents(static::ENDPOINT);
        return json_decode($rawData);
    }

    /**
     * @param $exc
     * @return bool|string
     */
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
                        'converted_value' => round((float)$value / $rate->buy * $this->supplement, 9),
                        'rate' => round($rate->buy * $this->supplement, 9)
                    ];
                }
            } else {
                if ($rate->symbol == $to) {
                    $this->rates += [
                        'currency_from' => $from,
                        'currency_to' => $to,
                        'value' => $value,
                        'converted_value' => round($rate->buy * (float)$value * $this->supplement, 2),
                        'rate' => round($rate->buy * $this->supplement, 9)
                    ];
                }
            }
        }
        return $this->checkRes();
    }

    /**
     * @param $exc
     * @return bool|string
     */
    public function getRates($exc = 'all')
    {
        return $this->rates($exc);
    }

    /**
     * @param $from
     * @param $to
     * @param $value
     * @return bool|string
     */
    public function getConvert($from, $to, $value)
    {
        return $this->convert($from, $to, $value);
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return static::ENDPOINT;
    }

    /**
     * @return false|string
     */
    public function getResponseSuccess()
    {
        return json_encode(['status' => "success", 'code' => 200, 'data' => $this->rates]);
    }

    /**
     * @return false|string
     */
    public function getResponseNotFound()
    {
        return json_encode(['status' => "error", 'code' => 404, 'message' => "Given Symbol Not Found"]);
    }
}
