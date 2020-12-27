<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\RolesManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /** @var UserRepository */
    private $userRepository;

    /** @var RolesManager */
    private $rolesManager;

    public function __construct(UserRepository $userRepository, RolesManager $rolesManager)
    {
        $this->userRepository = $userRepository;
        $this->rolesManager = $rolesManager;
    }

    public function storeAction(Request $request)
    {

    }

    /**
     * @Route("/users/show/{id}", name="users.show", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function showAction(int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->userRepository->find($id);

        if (!$user) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'is_admin' => $this->rolesManager->isAdmin($user)
        ]);
    }
}
