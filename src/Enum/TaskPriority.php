<?php

namespace App\Enum;

enum TaskPriority: string
{
    case LOW = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH = 'HIGH';
    case URGENT = 'URGENT';
}