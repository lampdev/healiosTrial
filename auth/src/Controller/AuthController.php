<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthController extends AbstractController
{
    /**
     * @Route("/api/register", name="auth.register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAction(Request $request): JsonResponse
    {
        // @todo: call crud@store

        return new JsonResponse(['success' => true]);
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
