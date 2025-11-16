<?php
declare(strict_types=1);

namespace App\Domain\Payments\Events;

use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static PendingDispatch dispatch(string $orderId, string $sourceOrderId)
 */
class CheckoutAuthorizationSuccessEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;
    use Broadcastable;

    public function __construct(public string $orderId, public string $sourceOrderId)
    {
    }
}
