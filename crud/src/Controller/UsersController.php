<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Requests\UserRequest;
use App\Responses\UserResponse;
use App\Services\RolesManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

class UsersController extends AbstractController
{
    /** @var UserRepository */
    private $userRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /** @var RolesManager */
    private $rolesManager;

    /** @var UserRequest */
    private $userRequest;

    /** @var UserResponse */
    private $userResponse;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        RolesManager $rolesManager,
        UserRequest $userRequest,
        UserResponse $userResponse
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->rolesManager = $rolesManager;
        $this->userRequest = $userRequest;
        $this->userResponse = $userResponse;
    }

    /**
     * @Route("/users/store", name="users.store", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function storeAction(Request $request): JsonResponse
    {
        $this->setUserRequest($request);
        $violations = $this->validateUserRequest();

        if (count($violations) > 0) {
            return new JsonResponse(['errors' => (string)$violations], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($this->userRepository->findOneBy(['email' => $this->userRequest->email])) {
            return new JsonResponse(['errors' => 'This email is already in use'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();
        $user->setName($this->userRequest->name);
        $user->setEmail($this->userRequest->email);
        $user->setPassword($this->userRequest->password);
        $user->setRole($this->userRequest->role);
        $this->userRepository->plush($user);
        $this->setUserResponse($user);

        return new JsonResponse($this->userResponse);
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

        $this->setUserResponse($user);

        return new JsonResponse($this->userResponse);
    }

    /**
     * @param Request $request
     */
    private function setUserRequest(Request $request): void
    {
        $roleId = (int)$request->get('role_id', 0);
        $role = $this->rolesManager->findOrDefault($roleId);
        $this->userRequest->name = (string)$request->get('name', '');
        $this->userRequest->email = (string)$request->get('email', '');
        $this->userRequest->password = (string)$request->get('password', '');
        $this->userRequest->role = $role;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    private function validateUserRequest(): ConstraintViolationListInterface
    {
        $validator = Validation::createValidator();

        $violations = $validator->validate($this->userRequest->name, [
            new NotBlank(['message' => 'Name is required']),
            new Length([
                'min' => 2,
                'max' => 255,
                'minMessage' => 'Name is too short',
                'maxMessage' => 'Name is too long'
            ]),
        ]);

        $violations->addAll(
            $validator->validate($this->userRequest->password, [
                new NotBlank(['message' => 'Password is required']),
                new Length([
                    'min' => 8,
                    'max' => 255,
                    'minMessage' => 'Password is too short',
                    'maxMessage' => 'Password is too long'
                ]),
                new NotCompromisedPassword(['message' => 'This password was compromised'])
            ])
        );

        $violations->addAll(
            $validator->validate($this->userRequest->email, [
                new NotBlank(['message' => 'Email is required']),
                new Email(['message' => 'Invalid email'])
            ])
        );

        $violations->addAll(
            $validator->validate($this->userRequest->role, [
                new NotNull(['message' => 'Role is required'])
            ])
        );

        return $violations;
    }

    /**
     * @param User $user
     */
    public function setUserResponse(User $user): void
    {
        $this->userResponse->id = $user->getId();
        $this->userResponse->name = $user->getName();
        $this->userResponse->email = $user->getEmail();
        $this->userResponse->isAdmin =  $this->rolesManager->isAdmin($user);
    }
}
