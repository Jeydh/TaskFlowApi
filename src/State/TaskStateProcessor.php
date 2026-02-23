<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProcessorInterface<Task, Task>
 */
final class TaskStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security,
    ) {
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // @phpstan-ignore-next-line Defensive runtime guard: only process Task entities, delegate others
        if (!$data instanceof Task) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $now = new \DateTimeImmutable();

        // POST: auto-assign creator
        if (($context['collection_operation_name'] ?? null) === 'post' || $operation->getName() === '_api_/tasks{._format}_post') {
            $user = $this->security->getUser();

            if (!$user instanceof User) {
                throw new \LogicException('Authenticated user not found.');
            }

            if ($data->getCreatedBy() === null) {
                $data->setCreatedBy($user);
            }

            if ($data->getCreatedAt() === null) {
                $data->setCreatedAt($now);
            }

            $data->setUpdatedAt($now);
        }

        // PATCH: bump updatedAt
        if (($context['item_operation_name'] ?? null) === 'patch' || str_contains((string) $operation->getName(), '_patch')) {
            $data->setUpdatedAt($now);
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}