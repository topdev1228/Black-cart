<?php
declare(strict_types=1);

namespace App\Domain\Billings\Events;

use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(SubscriptionValue $subscription)
 */
class SubscriptionActivatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;
    use Broadcastable;

    /**
     * Create a new event instance.
     */
    public function __construct(public SubscriptionValue $subscription)
    {
    }
}
