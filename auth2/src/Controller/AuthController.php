<?php

namespace App\Controller;

use App\Models\User;
use App\Services\GuzzleRequestExceptionTransformer;
use App\Services\GuzzleResponseTransformer;
use App\Services\JsonRequestDataKeeper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
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

    /** @var JWTTokenManagerInterface */
    private $tokenManager;

    /** @var AuthenticationSuccessHandler */
    private $authHandler;

    public function __construct(JWTTokenManagerInterface $tokenManager, AuthenticationSuccessHandler $authHandler)
    {
        $this->guzzleClient = new Client();
        $this->crudHost = (string)getenv('CRUD_HOST');
        $this->tokenManager = $tokenManager;
        $this->authHandler = $authHandler;
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request = JsonRequestDataKeeper::keepJson($request);
        $email = (string)$request->get('email', '');

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
        $token = $this->tokenManager->createFromPayload($user, $response);
        $this->authHandler->handleAuthenticationSuccess($user, $token);

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAction(Request $request): JsonResponse
    {
        $request = JsonRequestDataKeeper::keepJson($request);

        try {
            $response = $this->guzzleClient->post($this->crudHost . '/api/users/store', [
                'json' => [
                    'name' => (string)$request->get('name', ''),
                    'email' => (string)$request->get('email', ''),
                    'password' => (string)$request->get('password', '')
                ]
            ]);
        } catch (RequestException $e) {
            return new JsonResponse(['errors' => GuzzleRequestExceptionTransformer::toString($e)], $e->getCode());
        } catch (GuzzleException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(GuzzleResponseTransformer::toArray($response), Response::HTTP_OK);
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
