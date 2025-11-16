<?php
declare(strict_types=1);

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(Order $order)
 */
class OrderCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    public OrderValue $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = OrderValue::from($order);
    }
}
