<?php

namespace App\Enum;

use App\Enum\Traits\StringBackedEnumTrait;

enum TaskStatus: string
{
    use StringBackedEnumTrait;

    public const VALUES = [
        self::TODO->value,
        self::IN_PROGRESS->value,
        self::BLOCKED->value,
        self::DONE->value,
        self::ARCHIVED->value,
    ];

    case TODO = 'TODO';
    case IN_PROGRESS = 'IN_PROGRESS';
    case BLOCKED = 'BLOCKED';
    case DONE = 'DONE';
    case ARCHIVED = 'ARCHIVED';
}