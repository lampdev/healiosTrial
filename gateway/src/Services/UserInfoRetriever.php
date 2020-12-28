<?php

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;

class UserInfoRetriever
{
    /** @var string */
    private $authHost;

    /** @var CustomGuzzleClient */
    private $customGuzzleClient;

    /** @var string */
    private $crudHost;

    public function __construct(CustomGuzzleClient $customGuzzleClient)
    {
        $this->authHost = (string)getenv('AUTH_HOST');
        $this->crudHost = (string)getenv('CRUD_HOST');
        $this->customGuzzleClient = $customGuzzleClient;
    }

    /**
     * @param int $userId
     * @return bool|null
     */
    public function isAdmin(int $userId): ?bool
    {
        $url = $this->crudHost . '/api/users/show/' . $userId;

        try {
            $response = $this->customGuzzleClient->request(Request::METHOD_GET, $url);
        } catch (GuzzleException $e) {
            return null;
        }

        return (bool)$response->arrayData['isAdmin'];
    }

    /**
     * @param Request $request
     * @return int|null
     */
    public function getUserIdByToken(Request $request): ?int
    {
        $token = (string)$request->headers->get('Authorization', '');

        try {
            $response = $this->customGuzzleClient->request(Request::METHOD_GET, $this->authHost . '/api/current', [
                'headers' => [
                    'Authorization' => $token,
                    'Accept' => 'application/json'
                ]
            ]);
        } catch (GuzzleException $e) {
            return null;
        }

        return (int)$response->arrayData['id'];
    }
}
