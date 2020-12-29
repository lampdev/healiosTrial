<?php

namespace App\Tests\Feature;

use App\Tests\TestCases\FeatureTestCase;

class UsersApiTests extends FeatureTestCase
{
    public function testShow(): void
    {
        $this->loginAsUser();
        $response = $this->getArrayResponse();
        $token = $response['token'];
        $this->get('/api/users/show/' . self::EXISTING_USER_ID, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertResponseOk();
        $expectedResponse = [
            'id' => self::EXISTING_USER_ID,
            'name' => self::EXISTING_USER_NAME,
            'email' => self::EXISTING_USER_EMAIL,
            'isAdmin' => false,
        ];
        $this->assertEquals($expectedResponse, $this->getArrayResponse());
    }
}
