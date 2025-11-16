<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Enums;

enum JobStatus: string
{
    case CANCELED = 'canceled';
    case CANCELING = 'canceling';
    case COMPLETED = 'completed';
    case CREATED = 'created';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case RUNNING = 'running';
}
