<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\CustomGuzzleClient;
use App\Services\RequestDataParser;
use App\Services\RequestExceptionParser;
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
    private const DEFAULT_ROLE_ID = 0;

    /** @var CustomGuzzleClient */
    private $customGuzzleClient;

    /** @var string */
    private $crudHost;

    public function __construct(CustomGuzzleClient $customGuzzleClient)
    {
        $this->customGuzzleClient = $customGuzzleClient;
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
        $request = RequestDataParser::transformJsonBody($request);
        $password = (string)$request->get('password', '');

        try {
            $response = $this->customGuzzleClient->post($this->crudHost . '/api/users/find-by-credentials', [
                'json' => [
                    'email' => (string)$request->get('username', ''),
                    'password' => $password
                ]
            ]);
        } catch (GuzzleException $e) {
            return new JsonResponse(['errors' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $responseData = $response->arrayData;
        $user = new User($responseData['id'], $responseData['email'], $responseData['password']);
        $token = $tokenManager->create($user);
        $handler->handleAuthenticationSuccess($user, $token);
        dd($token);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @param CustomGuzzleClient $guzzleClient
     * @return JsonResponse
     */
    public function registerAction(Request $request, CustomGuzzleClient $guzzleClient): JsonResponse
    {
        try {
            $response = $guzzleClient->post(getenv('CRUD_HOST') . '/api/users/store', [
                'json' => [
                    'name' => (string)$request->get('name', ''),
                    'email' => (string)$request->get('email', ''),
                    'password' => (string)$request->get('password', ''),
                    'role_id' => self::DEFAULT_ROLE_ID,
                ]
            ]);
        } catch (RequestException $e) {
            return new JsonResponse(['errors' => RequestExceptionParser::getErrors($e)], $e->getCode());
        } catch (GuzzleException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($response->arrayData, $response->statusCode);
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

        return new JsonResponse(['id' => $user->getId()]);
    }
}
