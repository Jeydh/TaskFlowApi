<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;

abstract class ApiTestCaseBase extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function getAuthTokens(
        Client $client, 
        string $email = 'test@example.com', 
        string $password = 'test1234'
    ): array
    {
        $response = $client->request('POST', '/api/login', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);
        $data = $response->toArray(false);
        return $data;
    }
}
