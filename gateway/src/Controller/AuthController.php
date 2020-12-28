<?php


namespace App\Controller;


use App\Services\CustomGuzzleClient;
use App\Services\RequestDataParser;
use App\Services\RequestExceptionParser;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AuthController extends AbstractController
{
    /**
     * @Route("/api/register", name="auth.register", methods={"POST"})
     * @param Request $request
     * @param CustomGuzzleClient $guzzleClient
     * @return JsonResponse
     */
    public function registerAction(Request $request, CustomGuzzleClient $guzzleClient): JsonResponse
    {
        $request = RequestDataParser::transformJsonBody($request);

        try {
            $response = $guzzleClient->post(getenv('AUTH_HOST') . '/api/register', [
                'form_params' => [
                    'name' => (string)$request->get('name', ''),
                    'email' => (string)$request->get('email', ''),
                    'password' => (string)$request->get('password', ''),
                ]
            ]);
        } catch (RequestException $e) {
            return new JsonResponse(['errors' => RequestExceptionParser::getErrors($e)], $e->getCode());
        } catch (GuzzleException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($response->arrayData, $response->statusCode);
    }

    /**
     * @Route("/api/login", name="auth.login", methods={"POST"})
     * @param Request $request
     * @param CustomGuzzleClient $guzzleClient
     * @return JsonResponse
     */
    public function loginAction(Request $request, CustomGuzzleClient $guzzleClient): JsonResponse
    {
        $request = RequestDataParser::transformJsonBody($request);

        try {
            $response = $guzzleClient->post(getenv('AUTH_HOST') . '/api/login', [
                'json' => [
                    'username' => (string)$request->get('username', ''),
                    'password' => (string)$request->get('password', ''),
                ]
            ]);
        } catch (RequestException $e) {
            return new JsonResponse(['errors' => RequestExceptionParser::getErrors($e)], $e->getCode());
        } catch (GuzzleException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($response->arrayData, $response->statusCode);
    }
}