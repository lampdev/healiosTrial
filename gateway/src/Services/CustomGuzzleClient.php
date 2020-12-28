<?php

namespace App\Services;

use App\Structures\ResponseData;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * @todo: Consider moving this service into a separate library
 * Class CustomGuzzleClient
 * @package App\Services
 */
class CustomGuzzleClient
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ResponseData
     * @throws GuzzleException
     */
    public function request(string $method, string $uri, array $options = []): ResponseData
    {
        $response = $this->client->request($method, $uri, $options);

        return $this->transformResponse($response);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseData
     */
    private function transformResponse(ResponseInterface $response): ResponseData
    {
        $responseData = new ResponseData();
        $responseData->statusCode = $response->getStatusCode();
        $arrayData = json_decode((string)$response->getBody(), true);

        if (!is_array($arrayData)) {
            $arrayData = [];
        }

        $responseData->arrayData = $arrayData;

        return $responseData;
    }
}
