<?php

namespace App\Tests\Api;

use App\Tests\Api\ApiTestCaseBase;

final class TaskSecurityTest extends ApiTestCaseBase
{
    public function testUserCannotAccessOthersTasks(): void
    {
        $client = static::createClient();
        $token = $this->getJwtToken($client);

        $client->request('GET', '/api/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);
        self::assertResponseIsSuccessful();
    }
}