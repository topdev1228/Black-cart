<?php
declare(strict_types=1);

namespace App\Domain\Payments\Events;

use App\Domain\Shared\Traits\Broadcastable;
use Brick\Money\Money;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static PendingDispatch dispatch(Money $authAmount, string $sourceOrderId)
 * @method static PendingDispatch dispatchSync(Money $authAmount, string $sourceOrderId)
 */
class ReAuthSuccessEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Money $authAmount, public string $sourceOrderId)
    {
    }

    public function broadcastWith(): array
    {
        return [
            'authAmount' => $this->authAmount->getMinorAmount()->toInt(),
            'currency' => $this->authAmount->getCurrency()->getCurrencyCode(),
            'sourceOrderId' => $this->sourceOrderId,
        ];
    }
}
