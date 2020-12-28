<?php

namespace App\Controller;

use App\Services\CustomGuzzleClient;
use App\Services\RequestDataParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="auth.")
 */
class AuthController extends ApiController
{
    /** @var string */
    private $authHost;

    public function __construct(CustomGuzzleClient $guzzleClient)
    {
        parent::__construct($guzzleClient);
        $this->authHost = (string)getenv('AUTH_HOST');
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
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

        return $this->apiRequest(Request::METHOD_POST, $this->authHost . '/api/register', $options);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
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

        return $this->apiRequest(Request::METHOD_POST, $this->authHost . '/api/login', $options);
    }
}
