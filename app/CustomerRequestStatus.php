<?php

namespace App\Enums;

enum CustomerRequestStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
