<?php
declare(strict_types=1);

namespace App\Domain\Payments\Events;

use App\Domain\Shared\Traits\Broadcastable;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/**
 * @method static PendingDispatch dispatch(Carbon $authExpiry, Money $authAmount, string $sourceOrderId)
 * @method static PendingDispatch dispatchSync(Carbon $authExpiry, Money $authAmount, string $sourceOrderId)
 */
class ReAuthFailedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    public function __construct(public CarbonImmutable $authExpiry, public Money $authAmount, public string $sourceOrderId)
    {
    }

    public function broadcastWith(): array
    {
        return [
            'authExpiry' => $this->authExpiry->format('Y-m-d\TH:i:s.u\Z'),
            'authAmount' => $this->authAmount->getMinorAmount()->toInt(),
            'currency' => $this->authAmount->getCurrency()->getCurrencyCode(),
            'sourceOrderId' => $this->sourceOrderId,
        ];
    }
}
