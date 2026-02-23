<?php

namespace App\Enum;

use App\Enum\Traits\StringBackedEnumTrait;

enum TaskPriority: string
{
    use StringBackedEnumTrait;

    public const VALUES = [
        self::LOW->value,
        self::MEDIUM->value,
        self::HIGH->value,
        self::URGENT->value,
    ];

    case LOW = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH = 'HIGH';
    case URGENT = 'URGENT';
}