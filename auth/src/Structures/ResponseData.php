<?php

namespace App\Structures;

use Symfony\Component\HttpFoundation\Response;

class ResponseData
{
    /** @var int */
    public $statusCode = Response::HTTP_OK;

    /** @var array */
    public $arrayData = [];

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->statusCode === Response::HTTP_OK;
    }
}
