<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HealiosTrial\Services\GuzzleResponseTransformer;
use Symfony\Component\HttpFoundation\Request;

class UserRetriever
{
    /** @var string */
    private $authHost;

    /** @var Client */
    private $guzzleClient;

    public function __construct()
    {
        $this->authHost = (string)getenv('AUTH_HOST');
        $this->guzzleClient = new Client();
    }

    /**
     * @param Request $request
     * @return User|null
     */
    public function getUserByToken(Request $request): ?User
    {
        try {
            $response = $this->guzzleClient->request(Request::METHOD_GET, $this->authHost . '/api/current', [
                'headers' => [
                    'Authorization' => (string)$request->headers->get('Authorization', ''),
                    'Accept' => 'application/json'
                ]
            ]);
        } catch (GuzzleException $e) {
            return null;
        }

        $response = GuzzleResponseTransformer::toArray($response);

        return User::createFromArray($response);
    }
}
