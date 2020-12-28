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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @Route("/users/store", name="users.store", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function storeAction(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
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
        $this->persistUser($user, $encoder);

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
     * @Route("/users/update/{id}", name="users.update", methods={"PUT"})
     * @param int $id
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function updateAction(int $id, Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $this->setUserRequest($request);
        $violations = $this->validateUserRequest();

        if (count($violations) > 0) {
            return new JsonResponse(['errors' => (string)$violations], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($this->userRepository->findOneBy(['email' => $this->userRequest->email])) {
            return new JsonResponse(['errors' => 'This email is already in use'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var User|null $user */
        $user = $this->userRepository->find($id);

        if (!$user) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->persistUser($user, $encoder);

        return new JsonResponse($this->userResponse);
    }

    /**
     * @Route("/users/delete/{id}", name="users.delete", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction(int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->userRepository->find($id);

        if (!$user) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $removed = $this->userRepository->delete($user);

        if (!$removed) {
            return new JsonResponse(['errors' => 'Entity was not removed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     */
    private function setUserRequest(Request $request): void
    {
        $data = json_decode($request->getContent(), true);
        $roleId = (int)$data['role_id'];
        $role = $this->rolesManager->findOrDefault($roleId);
        $this->userRequest->name = (string)$data['name'];
        $this->userRequest->email = (string)$data['email'];
        $this->userRequest->password = (string)$data['password'];
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
    private function setUserResponse(User $user): void
    {
        $this->userResponse->id = $user->getId();
        $this->userResponse->name = $user->getName();
        $this->userResponse->email = $user->getEmail();
        $this->userResponse->isAdmin =  $this->rolesManager->isAdmin($user);
    }

    /**
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     */
    private function persistUser(User $user, UserPasswordEncoderInterface $encoder): void
    {
        $user->setName($this->userRequest->name);
        $user->setEmail($this->userRequest->email);
        $user->setPassword($encoder->encodePassword($user, $this->userRequest->password));
        $user->setRole($this->userRequest->role);
        $this->userRepository->plush($user);
        $this->setUserResponse($user);
    }
}
