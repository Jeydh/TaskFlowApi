<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\Response as TestResponse;
use App\Tests\Api\ApiTestCaseBase;

final class TaskSecurityTest extends ApiTestCaseBase
{
    public function testAuthenticatedUserCanCreateTask(): void
    {
        $client = static::createClient();
        $tokens = $this->getAuthTokens($client);

        $this->createTaskAs(
            $tokens,
            [
                'title' => 'Test Task 1',
                'description' => 'This is a test task.'
            ]
        );

        self::assertResponseStatusCodeSame(201);
    }

    public function testAuthenticatedUserUpdateHisTasks(): void
    {
        $client = static::createClient();
        $owner = $this->getAuthTokens($client);

        $taskData = $this->createTaskAs($owner, ['title' => 'User 1 Task'])->toArray();

        $this->updateTaskAs($owner, $taskData['id'], ['title' => 'User 1 Task Updated']);

        self::assertResponseStatusCodeSame(200);
    }

    public function testAuthenticatedUserUpdateOthersTasks(): void
    {
        $client = static::createClient();
        $owner = $this->getAuthTokens($client);
        $other = $this->getAuthTokens($client, 'newuser@example.com', 'test1234');

        $taskData = $this->createTaskAs($owner, ['title' => 'User 1 Task'])->toArray();

        $this->updateTaskAs($other, $taskData['id'], ['title' => 'User 2 update Task']);

        self::assertResponseStatusCodeSame(404);
    }

    public function testAuthenticatedUserCanAccessOwnTasks(): void
    {
        $client = static::createClient();
        $login = $this->getAuthTokens($client);

        $this->getTaskAs($login, 2);
        self::assertResponseIsSuccessful();
    }

    public function testUnauthenticatedUserCannotAccessTasks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks/1');
        self::assertResponseStatusCodeSame(401);
    }

    public function testAuthenticatedUserCannotAccessOthersTasks(): void
    {
        $client = static::createClient();

        $owner = $this->getAuthTokens($client);
        $other = $this->getAuthTokens($client, 'newuser@example.com', 'test1234');

        $taskData = $this->createTaskAs($owner, ['title' => 'User 1 Task'])->toArray();
        self::assertResponseStatusCodeSame(201);

        $this->getTaskAs($other, $taskData['id']);

        self::assertResponseStatusCodeSame(404);
    }

    public function testAuthenticatedUserCanDeleteOwnTasks(): void
    {
        $client = static::createClient();
        $owner = $this->getAuthTokens($client);

        $taskData = $this->createTaskAs($owner, ['title' => 'User 1 Task'])->toArray();

        $this->deleteTaskAs($owner, $taskData['id']);

        self::assertResponseStatusCodeSame(204);
    }

    public function testAuthenticatedUserCannotDeleteOthersTasks(): void
    {
        $client = static::createClient();
        $owner = $this->getAuthTokens($client);
        $other = $this->getAuthTokens($client, 'newuser@example.com', 'test1234');

        $taskData = $this->createTaskAs($owner, ['title' => 'User 1 Task'])->toArray();

        $this->deleteTaskAs($other, $taskData['id']);

        self::assertResponseStatusCodeSame(404);
    }

    public function testAuthicatorTriesToDeleteDeletedTask(): void
    {
        $client = static::createClient();
        $owner = $this->getAuthTokens($client);

        $taskData = $this->createTaskAs($owner, ['title' => 'User 1 Task'])->toArray();

        $this->deleteTaskAs($owner, $taskData['id']);
        self::assertResponseStatusCodeSame(204);

        $this->deleteTaskAs($owner, $taskData['id']);
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @param array{
     *      token: string, 
     *      refresh_token: string
     * } $tokens The authentication tokens for the user.
     * @param array< string, mixed > $overrides Optional properties to override the default task data.
     * @return TestResponse The created task data.
     */
    private function createTaskAs(
        array $tokens,
        array $overrides = []
    ): TestResponse {
        $payload = array_merge([
            "title" => "Test Task",
            "description" => "This is a test task.",
            "status" => "todo",
            "priority" => "medium",
        ], $overrides);

        $client = static::createClient();
        $client->request('POST', '/api/tasks', [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer ' . $tokens['token'],
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json',
            ]
        ]);

        return $client->getResponse();
    }

    /**
     * @param array{
     *      token: string, 
     *      refresh_token: string
     * } $tokens The authentication tokens for the user.
     * @param int $taskDataId The ID of the task to update.
     * @param array< string, mixed > $overrides Optional properties to override the default task data.
     * @return TestResponse The updated task data.
     */
    private function updateTaskAs(
        array $tokens,
        int $taskDataId,
        array $overrides = []
    ): TestResponse {
        $payload = array_merge([
            "title" => "Test Task edited",
            "description" => "This is a test task.",
            "status" => "todo",
            "priority" => "medium",
        ], $overrides);

        $client = static::createClient();
        $client->request('PATCH', '/api/tasks/' . $taskDataId, [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer ' . $tokens['token'],
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        return $client->getResponse();
    }

    public function getTaskAs(
        array $tokens,
        int $taskDataId
    ): TestResponse {
        $client = static::createClient();
        $client->request('GET', '/api/tasks/' . $taskDataId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $tokens['token'],
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        return $client->getResponse();
    }

    public function deleteTaskAs(
        array $tokens,
        int $taskDataId
    ): TestResponse {
        $client = static::createClient();
        $client->request('DELETE', '/api/tasks/' . $taskDataId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $tokens['token'],
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        return $client->getResponse();
    }
}
