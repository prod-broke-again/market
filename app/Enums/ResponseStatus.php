<?php

namespace App\Enums;

enum ResponseStatus: string
{
    case ACTIVE = 'active';
    case AWAITING_CONFIRMATION = 'awaiting_confirmation';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
} 