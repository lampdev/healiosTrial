<?php

namespace App\Tests\Feature;

use App\Tests\TestCases\FeatureTestCase;

class AuthApiTests extends FeatureTestCase
{
    private const VALID_PASSWORD = 'SoMeSeCuRePaSsWoRd54535251!!!';

    public function testRegister(): void
    {
        $name = $this->getUniqueAndValidName();
        $email = $this->getUniqueAndValidEmail();
        $client = static::createClient();
        $client->request('POST', '/api/register', [
            'name' => $name,
            'email' => $email,
            'password' => self::VALID_PASSWORD,
        ]);
        $this->assertResponseOk($client);
        $response = $this->getArrayResponse($client);
        $this->assertArrayHasKey('id', $response);
        $this->assertGreaterThan(0, $response['id']);
        unset($response['id']);
        $expectedResponse = [
            'name' => $name,
            'email' => $email,
            'isAdmin' => false
        ];
        $this->assertEquals($expectedResponse, $response);
    }

    public function testLogin(): void
    {
        $client = static::createClient();
        $this->loginAsUser($client);
        $this->assertResponseOk($client);
        $response = $this->getArrayResponse($client);
        $this->assertArrayHasKey('token', $response);
        $token = $response['token'];
        $this->assertGreaterThan(0, strlen($token));
    }
}
