<?php

namespace App\Controller;

use App\Services\RequestDataParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends ApiController implements TokenAuthenticatedController
{
    /**
     * @Route("/api/users/store", name="users.store", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function storeAction(Request $request): JsonResponse
    {
        $request = RequestDataParser::transformJsonBody($request);
        $options = [
            'json' => [
                'name' => (string)$request->get('name', ''),
                'email' => (string)$request->get('email', ''),
                'password' => (string)$request->get('password', ''),
                'role_id' => (int)$request->get('role_id', 0)
            ]
        ];

        return $this->apiRequest(Request::METHOD_POST, getenv('CRUD_HOST') . '/users/store', $options);
    }

    /**
     * @Route("/api/users/show/{id}", name="users.show", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function showAction(int $id): JsonResponse
    {
        return $this->apiRequest(Request::METHOD_GET, getenv('CRUD_HOST') . '/users/show/' . $id);
    }

    /**
     * @Route("/api/users/update/{id}", name="users.update", methods={"PUT"})
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAction(int $id, Request $request): JsonResponse
    {
        $request = RequestDataParser::transformJsonBody($request);
        $options = [
            'json' => [
                'name' => (string)$request->get('name', ''),
                'email' => (string)$request->get('email', ''),
                'password' => (string)$request->get('password', ''),
                'role_id' => (int)$request->get('role_id', 0)
            ]
        ];

        return $this->apiRequest(Request::METHOD_PUT, getenv('CRUD_HOST') . '/users/update/' . $id, $options);
    }

    /**
     * @Route("/api/users/delete/{id}", name="users.delete", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction(int $id): JsonResponse
    {
        return $this->apiRequest(Request::METHOD_DELETE, getenv('CRUD_HOST') . '/users/delete/' . $id);
    }
}
