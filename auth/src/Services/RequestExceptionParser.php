<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;

/**
 * @todo: Consider moving this service into a separate library
 * Class RequestExceptionParser
 * @package App\Services
 */
class RequestExceptionParser
{
    /**
     * @param RequestException $exception
     * @return string
     */
    public static function getErrors(RequestException $exception): string
    {
        $body = (string)$exception->getResponse()->getBody();
        $body = json_decode($body, true);

        if (is_array($body) && array_key_exists('errors', $body)) {
            return (string)$body['errors'];
        }

        return $exception->getMessage();
    }
}
