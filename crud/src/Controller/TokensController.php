<?php

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use App\Repository\UserRepository;
use HealiosTrial\Services\JsonRequestDataKeeper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/tokens", name="tokens.")
 */
class TokensController
{
    /** @var UserRepository */
    private $userRepository;

    /** @var AccessTokenRepository */
    private $accessTokenRepository;

    public function __construct(UserRepository $userRepository, AccessTokenRepository $accessTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->accessTokenRepository = $accessTokenRepository;
    }

    /**
     * @Route("/store", name="store", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function storeAction(Request $request): JsonResponse
    {
        $request = JsonRequestDataKeeper::keepJson($request);
        $userId = (int)$request->get('user_id', 0);

        /** @var User|null $user */
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['errors' => 'User not found', Response::HTTP_NOT_FOUND]);
        }

        $token = (string)$request->get('token', '');
        $tokenModel = new AccessToken();
        $tokenModel->setToken($token);
        $tokenModel->setUser($user);
        $this->accessTokenRepository->plush($tokenModel);

        return new JsonResponse(['message' => 'Token stored']);
    }
}
