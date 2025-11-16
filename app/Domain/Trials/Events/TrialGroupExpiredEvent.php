<?php
declare(strict_types=1);

namespace App\Domain\Trials\Events;

use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method PendingDispatch static dispatch(string $groupKey)
 */
class TrialGroupExpiredEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    use Broadcastable;

    /**
     * Create a new event instance.
     */
    public function __construct(protected string $groupKey)
    {
    }
}
