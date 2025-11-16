<?php
declare(strict_types=1);

namespace App\Domain\Stores\Events;

use App\Domain\Shared\Traits\Broadcastable;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(Store $store)
 */
class StoreDeletedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;
    use Broadcastable;

    protected StoreValue $store;

    /**
     * Create a new event instance.
     */
    public function __construct(Store $storeModel)
    {
        $this->store = StoreValue::from($storeModel);
    }
}
