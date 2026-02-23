<?php

namespace App\Tests\Api;

use App\Tests\Api\ApiTestCaseBase;

final class AuthApiTest extends ApiTestCaseBase
{
    public function testLogin(): void
    {
        $response = static::createClient()->request('POST', '/api/login', [
            'json' => [
                'email' => 'test@example.com',
                'password' => 'test1234',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray(false);
        self::assertArrayHasKey('token', $data);
        self::assertIsString($data['token']);
        self::assertNotEmpty($data['token']);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $response = static::createClient()->request('POST', '/api/login', [
            'json' => [
                'email' => 'invalid@example.com',
                'password' => 'invalidpassword'
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }
}
