<?php
declare(strict_types=1);

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Models\Refund;
use App\Domain\Orders\Values\Refund as RefundValue;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(Refund $refund)
 */
class RefundCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    public RefundValue $refund;

    /**
     * Create a new event instance.
     */
    public function __construct(Refund $refund)
    {
        $this->refund = RefundValue::from($refund);
    }
}
