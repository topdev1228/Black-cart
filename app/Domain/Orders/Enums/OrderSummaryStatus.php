<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

/**
 * Order summary status for customer facing purposes
 */
enum OrderSummaryStatus: string
{
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case TRIAL_IN_PROGRESS = 'trial_in_progress';
    case COMPLETED = 'completed';
}
