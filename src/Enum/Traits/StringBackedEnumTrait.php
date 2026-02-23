<?php

namespace App\Enum\Traits;

trait StringBackedEnumTrait
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        $value = str_replace([' ', '-'], '_', $value);

        return self::tryFrom(strtoupper($value))?->value;
    }

    public static function values(): array
    {
        return array_map(
            static fn(self $case) => $case->value,
            self::cases()
        );
    }

    public static function names(): array
    {
        return array_map(
            static fn(self $case) => $case->name,
            self::cases()
        );
    }

    public static function exists(string $value): bool
    {
        return null !== self::tryFrom($value);
    }
}