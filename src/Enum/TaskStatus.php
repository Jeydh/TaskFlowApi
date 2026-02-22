<?php

namespace App\Enum;

enum TaskStatus: string
{
    case TODO = 'TODO';
    case IN_PROGRESS = 'IN_PROGRESS';
    case BLOCKED = 'BLOCKED';
    case DONE = 'DONE';
    case ARCHIVED = 'ARCHIVED';
}