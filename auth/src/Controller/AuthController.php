<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\CustomGuzzleClient;
use App\Services\RequestExceptionParser;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthController extends AbstractController
{
    private const DEFAULT_ROLE = 0;

    /**
     * @Route("/api/register", name="auth.register", methods={"POST"})
     * @param Request $request
     * @param CustomGuzzleClient $guzzleClient
     * @return JsonResponse
     */
    public function registerAction(Request $request, CustomGuzzleClient $guzzleClient): JsonResponse
    {
        try {
            $response = $guzzleClient->post(getenv('CRUD_HOST') . '/users/store', [
                'json' => [
                    'name' => (string)$request->get('name', ''),
                    'email' => (string)$request->get('email', ''),
                    'password' => (string)$request->get('password', ''),
                    'role_id' => self::DEFAULT_ROLE,
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
     * @Route("/api/current", name="auth.current", methods={"GET"})
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
