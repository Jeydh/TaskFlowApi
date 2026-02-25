<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Task\TaskCreateInput;
use App\Entity\Task;
use App\Entity\User;
use App\State\TaskStateProcessor;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProcessorInterface<TaskCreateInput, Task>
 */
final class TaskCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private TaskStateProcessor $taskStateProcessor,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // @phpstan-ignore-next-line Defensive runtime guard: only process Task entities, delegate others
        if (!$data instanceof TaskCreateInput) {
            throw new \InvalidArgumentException('Expected TaskCreateInput.');
        }

        $now = new \DateTimeImmutable();

        /** @var User $user */
        $user = $this->security->getUser();

        $task = new Task();
        $task->setTitle($data->title);
        $task->setDescription($data->description ?? null);
        $task->setStatus(strtoupper($data->status));
        $task->setPriority(strtoupper($data->priority));
        $task->setCreatedBy($user);
        $task->setCreatedAt($now);


        if (null !== $data->dueDate) {
            $task->setDueDate(new \DateTimeImmutable($data->dueDate->format('Y-m-d\TH:i:sP')));
        }

        return $this->taskStateProcessor->process($task, $operation, $uriVariables, $context);
    }
}