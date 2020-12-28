<?php

namespace App\Controller;

use App\Services\CustomGuzzleClient;
use App\Services\RequestExceptionParser;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /** @var CustomGuzzleClient */
    protected $customGuzzleClient;

    public function __construct(CustomGuzzleClient  $guzzleClient)
    {
        $this->customGuzzleClient = $guzzleClient;
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
            $response = $this->customGuzzleClient->request($method, $url, $options);
        } catch (RequestException $e) {
            return new JsonResponse(['errors' => RequestExceptionParser::getErrors($e)], $e->getCode());
        } catch (GuzzleException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($response->arrayData, $response->statusCode);
    }
}
