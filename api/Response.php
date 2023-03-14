<?php

namespace Main;

/**
 * Class Response
 */
class Response
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';

    /** @var int */
    public int $httpStatusCode = 200;

    /** @var string */
    public string $message = '';

    /** @var array */
    public array $data = [];

    /**
     * @return array
     */
    public function getBaseResponse(): array
    {
        return [
            'status' => $this->getStatus(),
            'message' => $this->message
        ];
    }

    /**
     * @return false|string
     */
    public function getResponseSuccess()
    {
        $response = $this->getBaseResponse();
        $response['data'] = $this->data;
        unset($response['message']);

        return $this->getEncodeResponse($response);
    }

    /**
     * @return false|string
     */
    public function getResponseInvalidValue()
    {
        $this->message = 'Invalid value';
        $this->httpStatusCode = 400;

        return $this->getEncodeResponse($this->getBaseResponse());
    }

    /**
     * @return false|string
     */
    public function getResponseMethodNotFound()
    {
        $this->message = 'Method not allowed';
        $this->httpStatusCode = 404;

        return $this->getEncodeResponse($this->getBaseResponse());
    }

    /**
     * @return false|string
     */
    public function getResponseSymbolNotFound()
    {
        $this->message = 'Given Symbol Not Found';
        $this->httpStatusCode = 404;

        return $this->getEncodeResponse($this->getBaseResponse());
    }

    /**
     * @return false|string
     */
    public function getResponseInvalidToken()
    {
        $this->message = 'Invalid token';
        $this->httpStatusCode = 401;

        return $this->getEncodeResponse($this->getBaseResponse());
    }

    /**
     * @param array $response
     * @return false|string
     */
    public function getEncodeResponse(array $response)
    {
        http_response_code($this->httpStatusCode);
        return json_encode($response);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->httpStatusCode < 300
            ? static::STATUS_SUCCESS
            : static::STATUS_ERROR;
    }
}