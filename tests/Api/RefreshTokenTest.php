<?php

namespace App\Tests\Api;

use App\Tests\Api\ApiTestCaseBase;

final class RefreshTokenTest extends ApiTestCaseBase
{
    public function testRefreshTokenReturnsNewToken(): void
    {
        $client = static::createClient();
        $login = $this->getAuthTokens($client);

        $client->request('POST', '/api/token/refresh', [
            'json' => [
                'refresh_token' => $login['refresh_token'],
            ],
        ]);
        self::assertResponseIsSuccessful();

        $refreshData = $client->getResponse()->toArray();

        self::assertArrayHasKey('token', $refreshData);
        self::assertArrayHasKey('refresh_token', $refreshData);

        self::assertIsString($refreshData['token']);
        self::assertIsString($refreshData['refresh_token']);
        self::assertNotEmpty($refreshData['token']);
        self::assertNotEmpty($refreshData['refresh_token']);

        self::assertNotSame($login['token'], $refreshData['token']);
        self::assertNotSame($login['refresh_token'], $refreshData['refresh_token']);
    }

    public function testUsedRefreshTokenCannotBeUsedAgain(): void
    {
        $client = static::createClient();
        $login = $this->getAuthTokens($client); // token and refresh token

        $client->request('POST', '/api/token/refresh', [
            'json' => [
                'refresh_token' => $login['refresh_token'],
            ],
        ]);
        self::assertResponseIsSuccessful();

        $refreshData = $client->getResponse()->toArray(); // token2 and refresh token2
        self::assertArrayHasKey('token', $refreshData); 

        $client->request('POST', '/api/token/refresh', [
            'json' => [
                'refresh_token' => $login['refresh_token'], // trying to reuse the same refresh token
            ],
        ]);
        self::assertResponseStatusCodeSame(401); // should fail because the refresh token has already been used
    }

    public function testRefreshTokenRequiresPayload(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/token/refresh', [
            'json' => [], // no refresh token provided
        ]);
        self::assertResponseStatusCodeSame(401); // should return bad request due to missing refresh token
    }

    public function testInvalidRefreshTokenReturnsUnauthorized(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/token/refresh', [
            'json' => [
                'refresh_token' => 'invalid_refresh_token',
            ],
        ]);
        self::assertResponseStatusCodeSame(401);
    }
}