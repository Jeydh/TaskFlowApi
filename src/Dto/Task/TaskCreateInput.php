<?php

namespace App\Dto\Task;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Dto\Task\Shared\ValidateTaskInputEnumsTrait;

final class TaskCreateInput
{
    use ValidateTaskInputEnumsTrait;

    #[Groups(['task:write'])]
    #[Assert\NotBlank]
    public string $title;

    #[Groups(['task:write'])]
    public ?string $description = null;

    #[Groups(['task:write'])]
    #[Assert\NotBlank]
    public string $status;

    #[Groups(['task:write'])]
    #[Assert\NotBlank]
    public string $priority;

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
