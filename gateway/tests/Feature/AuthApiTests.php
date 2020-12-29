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
        $this->post('/api/register', [
            'name' => $name,
            'email' => $email,
            'password' => self::VALID_PASSWORD,
        ]);
        $this->assertResponseOk();
        $response = $this->getArrayResponse();
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
        $this->loginAsUser();
        $this->assertResponseOk();
        $response = $this->getArrayResponse();
        $this->assertArrayHasKey('token', $response);
        $token = $response['token'];
        $this->assertGreaterThan(0, strlen($token));
    }

    /**
     * @return string
     */
    private function getUniqueAndValidName(): string
    {
        return 'someName' . rand(0, 100) . microtime(true);
    }

    /**
     * @return string
     */
    private function getUniqueAndValidEmail(): string
    {
        return 'someEmail' . rand(0, 100) . microtime(true) . '@email.com';
    }
}
