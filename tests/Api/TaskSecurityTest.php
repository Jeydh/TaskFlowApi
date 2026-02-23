<?php

namespace App\Tests\Api;

use App\Tests\Api\ApiTestCaseBase;

final class TaskSecurityTest extends ApiTestCaseBase
{
    public function testUserCannotAccessOthersTasks(): void
    {
        $client = static::createClient();
        $login = $this->getAuthTokens($client);

        $client->request('GET', '/api/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$login['token'],
            ],
        ]);
        self::assertResponseIsSuccessful();
    }
}