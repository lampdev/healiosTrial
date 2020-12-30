<?php

namespace App\Services;

use App\Models\User;
use App\Wrappers\GuzzleClientWrapper;
use GuzzleHttp\Exception\GuzzleException;
use HealiosTrial\Services\GuzzleResponseTransformer;
use Symfony\Component\HttpFoundation\Request;

class UserRetriever
{
    /** @var string */
    private $authHost;

    /** @var GuzzleClientWrapper */
    private $guzzleClientWrapper;

    public function __construct(GuzzleClientWrapper $guzzleClientWrapper)
    {
        $this->guzzleClientWrapper = $guzzleClientWrapper;
        $this->authHost = (string)getenv('AUTH_HOST');

    }

    /**
     * @param Request $request
     * @return User|null
     */
    public function getUserByToken(Request $request): ?User
    {
        try {
            $response = $this->guzzleClientWrapper->request(Request::METHOD_GET, $this->authHost . '/api/current', [
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
