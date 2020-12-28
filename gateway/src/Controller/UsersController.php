<?php

namespace App\Controller;

use App\Services\CustomGuzzleClient;
use App\Services\RequestDataParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/users", name="users.")
 */
class UsersController extends ApiController implements TokenAuthenticatedController
{
    /** @var string */
    private $crudHost;

    public function __construct(CustomGuzzleClient $guzzleClient)
    {
        parent::__construct($guzzleClient);
        $this->crudHost = (string)getenv('CRUD_HOST');
    }

    /**
     * @Route("/store", name="store", methods={"POST"})
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

        return $this->apiRequest(Request::METHOD_POST, $this->crudHost . '/api/users/store', $options);
    }

    /**
     * @Route("/show/{id}", name="show", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function showAction(int $id): JsonResponse
    {
        return $this->apiRequest(Request::METHOD_GET, $this->crudHost . '/api/users/show/' . $id);
    }

    /**
     * @Route("/update/{id}", name="update", methods={"PUT"})
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

        return $this->apiRequest(Request::METHOD_PUT, $this->crudHost . '/api/users/update/' . $id, $options);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction(int $id): JsonResponse
    {
        return $this->apiRequest(Request::METHOD_DELETE, $this->crudHost . '/api/users/delete/' . $id);
    }
}
