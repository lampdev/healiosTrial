<?php

namespace App\Tests\Features;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthApiTests extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [
            'name' => 'test',
            'email' => 'test@email.com',
            'password' => '1q2w3e4r5ttesting**',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLogin()
    {
        $client = static::createClient();

        $client->request('POST', '/api/login', [
            'username' => 'admin@email.com',
            'password' => 'password',
        ]);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $content = (string)$response->getContent();
        $content = json_decode($content, true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('token', $content);
        $this->assertTrue(strlen($content['token']) > 0);
    }
}