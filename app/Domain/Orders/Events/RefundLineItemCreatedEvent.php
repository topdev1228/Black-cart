<?php
declare(strict_types=1);

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Values\RefundLineItem as RefundLineItemValue;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(RefundLineItemValue $refundLineItemValue)
 */
class RefundLineItemCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public RefundLineItemValue $refundLineItemValue)
    {
    }
}
