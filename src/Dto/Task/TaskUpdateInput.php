<?php

namespace App\Dto\Task;

use Symfony\Component\Serializer\Attribute\Groups;
use App\Dto\Task\Shared\ValidateTaskInputEnumsTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class TaskUpdateInput
{
    use ValidateTaskInputEnumsTrait;

    #[Groups(['task:write'])]
    public ?string $title = null;

    #[Groups(['task:write'])]
    public ?string $description = null;

    #[Groups(['task:write'])]
    public ?string $status = null;

    #[Groups(['task:write'])]
    public ?string $priority = null;

    #[Groups(['task:write'])]
    public ?int $assignedToId = null;

    #[Groups(['task:write'])]
    public ?\DateTimeImmutable $dueDate = null;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        $this->validateStatusEnum($context);
        $this->validatePriorityEnum($context);
    }
}
