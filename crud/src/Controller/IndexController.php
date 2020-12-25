<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET","HEAD","OPTIONS"})
     * @return JsonResponse
     */
    public function indexAction(): JsonResponse
    {
        return new JsonResponse();
    }
}
