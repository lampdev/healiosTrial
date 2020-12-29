<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

/**
 * @todo: Consider moving this service into a separate library
 */
class JsonRequestDataKeeper
{
    /**
     * @param Request $request
     * @return Request
     */
    public static function keepJson(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
