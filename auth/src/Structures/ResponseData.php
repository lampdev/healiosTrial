<?php

namespace App\Structures;

use Symfony\Component\HttpFoundation\Response;

/**
 * @todo Consider moving this structure into a separate library
 * Class ResponseData
 * @package App\Structures
 */
class ResponseData
{
    /** @var int */
    public $statusCode = Response::HTTP_OK;

    /** @var array */
    public $arrayData = [];
}
