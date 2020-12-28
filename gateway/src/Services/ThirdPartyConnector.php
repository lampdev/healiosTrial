<?php

namespace App\Services;

use App\Structures\UserData;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;

class ThirdPartyConnector
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
     * @return UserData|null
     */
    public function getUser(int $userId): ?UserData
    {
        try {
            $response = $this->customGuzzleClient->request(
                Request::METHOD_GET,
                $this->crudHost . '/users/show/' . $userId
            );
        } catch (GuzzleException $e) {
            return null;
        }

        $userData = new UserData();
        $userData->userId = (int)$response->arrayData['id'];
        $userData->isAdmin = (bool)$response->arrayData['isAdmin'];

        return $userData;
    }

    /**
     * @param Request $request
     * @return int|null
     */
    public function validateToken(Request $request): ?int
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
