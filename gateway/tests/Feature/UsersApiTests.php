<?php

namespace App\Tests\Feature;

use App\Tests\TestCases\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

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
        $response = $this->getArrayResponse();
        $expectedResponse = [
            'id' => self::EXISTING_USER_ID,
            'name' => self::EXISTING_USER_NAME,
            'email' => self::EXISTING_USER_EMAIL,
            'isAdmin' => false,
        ];
        $this->assertEquals($expectedResponse, $response);
    }

    public function testUpdate(): void
    {
        $newUserData = $this->registerAndLoginAsNewUser();
        $newEmail = $this->getNonExistingValidEmail();
        $newPassword = 'NeWPasWord!2341**';
        $newName = 'SomeName';
        $newData = [
            'name' => $newName,
            'email' => $newEmail,
            'password' => $newPassword,
        ];
        $this->put('/api/users/update/' . $newUserData['id'], $newData, [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $newUserData['token'],
            'CONTENT_TYPE' => 'application/json',
        ]);
        $response = $this->getArrayResponse();
        $this->assertResponseOk();
        $expectedResponse = [
            'id' => $newUserData['id'],
            'name' => $newName,
            'email' => $newEmail,
            'isAdmin' => false
        ];
        $this->assertEquals($expectedResponse, $response);
    }

    public function testDelete(): void
    {
        $newUserData = $this->registerAndLoginAsNewUser();
        $this->delete('/api/users/delete/' . $newUserData['id'], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $newUserData['token'],
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertResponseOk();
    }

    /**
     * @return array
     */
    private function registerAndLoginAsNewUser(): array
    {
        $email = $this->registerAsUser();
        $response = $this->getArrayResponse();
        $id = $response['id'];
        $this->loginAsUser($email, self::VALID_PASSWORD);
        $response = $this->getArrayResponse();
        $token = $response['token'];

        return [
            'id' => $id,
            'token' => $token
        ];
    }
}
