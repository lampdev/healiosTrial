<?php

namespace App\Tests\TestCases;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FeatureTestCase extends WebTestCase
{
    /**
     * @return string
     */
    protected function getUniqueAndValidName(): string
    {
        return 'someName' . rand(0, 100) . microtime(true);
    }

    /**
     * @return string
     */
    protected function getUniqueAndValidEmail(): string
    {
        return 'someEmail' . rand(0, 100) . microtime(true) . '@email.com';
    }

    /**
     * @param KernelBrowser $client
     */
    protected function loginAsUser(KernelBrowser $client): void
    {
        $client->request('POST', '/api/login', [
            'email' => 'user@email.com',
            'password' => 'password',
        ]);
    }

    /**
     * @param KernelBrowser $client
     * @return array
     */
    protected function getArrayResponse(KernelBrowser $client): array
    {
        $response = $client->getResponse();

        if (!$response instanceof JsonResponse) {
            return [];
        }

        return json_decode($response->getContent(), true);
    }

    /**
     * @param KernelBrowser $client
     */
    protected function assertResponseOk(KernelBrowser $client): void
    {
        $this->assertResponseStatus(Response::HTTP_OK, $client);
    }

    /**
     * @param int $code
     * @param KernelBrowser $client
     */
    protected function assertResponseStatus(int $code, KernelBrowser $client): void
    {
        $this->assertEquals($code, $client->getResponse()->getStatusCode());
    }
}
