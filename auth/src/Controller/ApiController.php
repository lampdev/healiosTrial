<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /** @var int */
    protected $statusCode = Response::HTTP_OK;

    /**
     * @return int
     */
    protected function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param array $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function response(array $data, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param string $error
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithErrors(string $error, $headers = []): JsonResponse
    {
        $data = [
            'status' => $this->getStatusCode(),
            'errors' => $error,
        ];

        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param string $success
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithSuccess(string $success, $headers = []): JsonResponse
    {
        $data = [
            'status' => $this->getStatusCode(),
            'success' => $success,
        ];

        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

//    /**
//     * @param string $message
//     * @return JsonResponse
//     */
//    protected function respondUnauthorized(string $message = 'Unauthorized'): JsonResponse
//    {
//        return $this->setStatusCode(Response::HTTP_UNAUTHORIZED)->respondWithErrors($message);
//    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function respondValidationError(string $message = 'Invalid data'): JsonResponse
    {
        return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->respondWithErrors($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound(string $message = 'Not found'): JsonResponse
    {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)->respondWithErrors($message);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    protected function respondCreated($data = []): JsonResponse
    {
        return $this->setStatusCode(Response::HTTP_CREATED)->response($data);
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    /**
     * @param int $statusCode
     * @return $this
     */
    protected function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
