<?php

namespace App\Tests\TestCases;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FeatureTestCase extends WebTestCase
{
    protected const EXISTING_USER_EMAIL = 'user@email.com';
    protected const EXISTING_USER_PASSWORD = 'password';

    /** @var KernelBrowser|null */
    private static $client = null;

    /**
     * @return KernelBrowser
     */
    protected static function getClient(): KernelBrowser
    {
        if (self::$client instanceof KernelBrowser) {
            return self::$client;
        }

        self::$client = static::createClient();

        return self::$client;
    }

    protected function loginAsUser(): void
    {
        $this->post('/api/login', [
            'email' => self::EXISTING_USER_EMAIL,
            'password' => self::EXISTING_USER_PASSWORD,
        ]);
    }

    /**
     * @return array
     */
    protected function getArrayResponse(): array
    {
        $response = self::getClient()->getResponse();

        if (!$response instanceof JsonResponse) {
            return [];
        }

        return json_decode($response->getContent(), true);
    }

    protected function assertResponseOk(): void
    {
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    /**
     * @param int $code
     */
    protected function assertResponseStatus(int $code): void
    {
        $this->assertEquals($code, self::getClient()->getResponse()->getStatusCode());
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @param array $files
     * @param array $server
     * @param string|null $content
     * @param bool $changeHistory
     * @return Crawler|null
     */
    protected function post(
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $changeHistory = true
    ): ?Crawler {
        return self::getClient()->request('POST', $uri, $parameters, $files, $server, $content, $changeHistory);
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @param array $files
     * @param array $server
     * @param string|null $content
     * @param bool $changeHistory
     * @return Crawler|null
     */
    protected function get(
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $changeHistory = true
    ): ?Crawler {
        return self::getClient()->request('GET', $uri, $parameters, $files, $server, $content, $changeHistory);
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @param array $files
     * @param array $server
     * @param string|null $content
     * @param bool $changeHistory
     * @return Crawler|null
     */
    protected function put(
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $changeHistory = true
    ): ?Crawler {
        return self::getClient()->request('PUT', $uri, $parameters, $files, $server, $content, $changeHistory);
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @param array $files
     * @param array $server
     * @param string|null $content
     * @param bool $changeHistory
     * @return Crawler|null
     */
    protected function delete(
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $changeHistory = true
    ): ?Crawler {
        return self::getClient()->request('DELETE', $uri, $parameters, $files, $server, $content, $changeHistory);
    }
}
