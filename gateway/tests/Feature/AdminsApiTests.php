<?php

namespace App\Tests\Feature;

use App\Tests\TestCases\FeatureTestCase;

class AdminsApiTests extends FeatureTestCase
{
    public function testAdminStore()
    {
        $this->loginAsAdmin();
        $response = $this->getArrayResponse();
        $token = $response['token'];
        $data = [
            'name' => self::VALID_NAME,
            'email' => $this->getNonExistingValidEmail(),
            'password' => self::VALID_PASSWORD,
            'role_id' => '2'
        ];
        $headers = [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json'
        ];
        $this->post('/api/users/store', $data, [], $headers);
        $this->assertResponseOk();
        $response = $this->getArrayResponse();
        $this->assertGreaterThan(0, $response['id']);
        unset($response['id']);
        $expectedResponse = [
            'name' => $data['name'],
            'email' => $data['email'],
            'isAdmin' => false
        ];
        $this->assertEquals($expectedResponse, $response);

    }

    public function testAdminShow()
    {
        $this->loginAsAdmin();
        $response = $this->getArrayResponse();
        $token = $response['token'];
        $this->get('/api/users/show/' . self::EXISTING_USER_ID, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertResponseOk();
        $response = $this->getArrayResponse();
        $expectedResponse = [
            'id' => self::EXISTING_USER_ID,
            'name' => self::EXISTING_USER_NAME,
            'email' => self::EXISTING_USER_EMAIL,
            'isAdmin' => false,
        ];
        $this->assertEquals($expectedResponse, $response);
    }
}