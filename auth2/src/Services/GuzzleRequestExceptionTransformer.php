<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;

/**
 * @todo: Consider moving this service into a separate library
 * @param RequestException $exception
 * @return string
 */
class GuzzleRequestExceptionTransformer
{
    public static function toString(RequestException $exception): string
    {
        $body = (string)$exception->getResponse()->getBody();
        $body = json_decode($body, true);

        if (is_array($body)) {
            if (array_key_exists('errors', $body)) {
                return (string)$body['errors'];
            }

            if (array_key_exists('message', $body)) {
                return (string)$body['message'];
            }
        }

        return $exception->getMessage();
    }
}
