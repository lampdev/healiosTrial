<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use HealiosTrial\Services\GuzzleRequestExceptionTransformer;
use HealiosTrial\Services\GuzzleResponseTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /** @var Client */
    protected $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return JsonResponse
     */
    protected function apiRequest(string $method, string $url, array $options = []): JsonResponse
    {
        try {
            $response = $this->guzzleClient->request($method, $url, $options);
        } catch (RequestException $e) {
            return new JsonResponse(['errors' => GuzzleRequestExceptionTransformer::toString($e)], $e->getCode());
        } catch (GuzzleException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $responseData = GuzzleResponseTransformer::toArray($response);

        return new JsonResponse($responseData, $response->getStatusCode());
    }
}
