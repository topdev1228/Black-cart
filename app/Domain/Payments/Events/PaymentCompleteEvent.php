<?php
declare(strict_types=1);

namespace App\Domain\Payments\Events;

use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCompleteEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public string $sourceOrderId, public bool $outstandingAmountZeroAlready = false)
    {
    }
}
