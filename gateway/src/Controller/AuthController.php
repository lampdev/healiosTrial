<?php

namespace App\Controller;

use App\Services\RequestDataParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends ApiController
{
    /**
     * @Route("/api/register", name="auth.register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAction(Request $request): JsonResponse
    {
        $request = RequestDataParser::transformJsonBody($request);
        $options = [
            'form_params' => [
                'name' => (string)$request->get('name', ''),
                'email' => (string)$request->get('email', ''),
                'password' => (string)$request->get('password', ''),
            ]
        ];

        return $this->apiRequest(Request::METHOD_POST, getenv('AUTH_HOST') . '/api/register', $options);
    }

    /**
     * @Route("/api/login", name="auth.login", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request): JsonResponse
    {
        $request = RequestDataParser::transformJsonBody($request);
        $options = [
            'json' => [
                'username' => (string)$request->get('username', ''),
                'password' => (string)$request->get('password', ''),
            ]
        ];

        // @todo: Get token, get userId and save

        return $this->apiRequest(Request::METHOD_POST, getenv('AUTH_HOST') . '/api/login', $options);
    }
}
