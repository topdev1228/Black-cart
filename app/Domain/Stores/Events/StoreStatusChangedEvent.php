<?php
declare(strict_types=1);

namespace App\Domain\Stores\Events;

use App\Domain\Shared\Traits\Broadcastable;
use App\Domain\Stores\Values\Store;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(Store $store, string $status)
 */
class StoreStatusChangedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;
    use Broadcastable;

    /**
     * Create a new event instance.
     */
    public function __construct(public Store $store, public string $status)
    {
    }
}
