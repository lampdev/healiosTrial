<?php

namespace App\Controller;

use App\Wrappers\GuzzleClientWrapper;
use HealiosTrial\Services\JsonRequestDataKeeper;
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

    public function __construct(GuzzleClientWrapper $guzzleClientWrapper)
    {
        parent::__construct($guzzleClientWrapper);
        $this->authHost = (string)getenv('AUTH_HOST');
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAction(Request $request): JsonResponse
    {
        $request = JsonRequestDataKeeper::keepJson($request);
        $options = [
            'json' => [
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
        $request = JsonRequestDataKeeper::keepJson($request);
        $options = [
            'json' => [
                'email' => (string)$request->get('email', ''),
                'password' => (string)$request->get('password', ''),
            ]
        ];

        return $this->apiRequest(Request::METHOD_POST, $this->authHost . '/api/login', $options);
    }
}
