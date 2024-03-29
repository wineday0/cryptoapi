<?php

namespace Main;

/**
 * Class Authorization
 */
class Authorization
{
    private const TOKEN = 'oFhN_7wC7RhgXW3jkpi-re2nWyI_EdhoM3oqtk-5vnO20maaQu-8Hhr-e1JaQHV';

    /**
     * Get header Authorization
     * @return string|null
     */
    private function getAuthorizationHeader(): ?string
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } else {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
                $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
                $requestHeaders = array_combine(
                    array_map('ucwords', array_keys($requestHeaders)),
                    array_values($requestHeaders)
                );
                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
        }
        return $headers;
    }

    /**
     * @return string|null
     */
    private function getBearerToken(): ?string
    {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->getBearerToken() === static::getPublicToken();
    }

    /**
     * @return string
     */
    public static function getPublicToken(): string
    {
        return static::TOKEN;
    }
}
