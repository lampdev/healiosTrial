<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @return JsonResponse
     */
    public function indexAction(): JsonResponse
    {
        return new JsonResponse();
    }
}
