<?php

namespace App\Controller;

use App\Models\User;
use App\Services\GuzzleResponseTransformer;
use App\Services\JsonRequestDataKeeper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api", name="auth.")
 */
class AuthController extends AbstractController
{
    /** @var Client */
    private $guzzleClient;

    /** @var string */
    private $crudHost;

    public function __construct()
    {
        $this->guzzleClient = new Client();
        $this->crudHost = (string)getenv('CRUD_HOST');
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @param JWTTokenManagerInterface $tokenManager
     * @param AuthenticationSuccessHandler $handler
     * @return JsonResponse
     */
    public function login(
        Request $request,
        JWTTokenManagerInterface $tokenManager,
        AuthenticationSuccessHandler $handler
    ) {
        $request = JsonRequestDataKeeper::keepJson($request);
        $email = (string)$request->get('username', '');

        try {
            $response = $this->guzzleClient->post($this->crudHost . '/api/users/find-by-credentials', [
                'json' => [
                    'email' => $email,
                    'password' => (string)$request->get('password', '')
                ]
            ]);
        } catch (GuzzleException $e) {
            return new JsonResponse(['errors' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $response = GuzzleResponseTransformer::toArray($response);
        $user = User::createFromPayload($email, $response);
        $token = $tokenManager->createFromPayload($user, $response);
        $handler->handleAuthenticationSuccess($user, $token);

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/current", name="current", methods={"GET"})
     * @param TokenStorageInterface $storage
     * @return JsonResponse
     */
    public function currentUserAction(TokenStorageInterface $storage)
    {
        /** @var User $user */
        $user = $storage->getToken()->getUser();

        return new JsonResponse($user->toArray());
    }
}
