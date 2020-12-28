<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

class RequestDataParser
{
    /**
     * @param Request $request
     * @return Request
     */
    public static function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
