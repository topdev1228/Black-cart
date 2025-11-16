<?php
declare(strict_types=1);

namespace App\Domain\Orders\Events;

use App\Domain\Shared\Traits\Broadcastable;
use Brick\Money\Money;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(string $orderId, string $sourceOrderId, string $trialGroupId, Money $amount))
 */
class PaymentRequiredEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;
    use Broadcastable;

    public function __construct(public string $orderId, public string $sourceOrderId, public string $trialGroupId, public Money $amount)
    {
    }

    public function broadcastWith(): mixed
    {
        return [
            'orderId' => $this->orderId,
            'sourceOrderId' => $this->sourceOrderId,
            'trialGroupId' => $this->trialGroupId,
            'amount' => $this->amount->getMinorAmount()->toInt(),
            'currency' => $this->amount->getCurrency()->getCurrencyCode(),
        ];
    }
}
