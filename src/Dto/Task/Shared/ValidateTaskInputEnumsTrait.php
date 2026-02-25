<?php

namespace App\Dto\Task\Shared;

use App\Enum\TaskStatus;
use App\Enum\TaskPriority;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

trait ValidateTaskInputEnumsTrait
{
    public function validateStatusEnum(ExecutionContextInterface $context): void
    {
        $this->validateEnumValue($this->status, 'status', [TaskStatus::class, 'normalize'], TaskStatus::values(), $context);
    }

    public function validatePriorityEnum(ExecutionContextInterface $context): void
    {
        $this->validateEnumValue($this->priority, 'priority', [TaskPriority::class, 'normalize'], TaskPriority::values(), $context);
    }

    private function validateEnumValue(?string $value, string $field, callable $normalizer, array $allowedValues, ExecutionContextInterface $context): void
    {
        if ($value === null || $value === '') {
            return;
        }
        $normalized = $normalizer($value);
        if (!in_array($normalized, $allowedValues, true)) {
            $context->buildViolation(sprintf('Invalid %s value.', $field))
                ->atPath($field)
                ->addViolation();
            }
    }
}

