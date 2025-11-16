<?php
declare(strict_types=1);

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Values\Trialable;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrialableDeliveredEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    public Trialable $trialable;

    /**
     * Create a new event instance.
     */
    public function __construct(string $lineItemId, string $sourceKey)
    {
        $this->trialable = Trialable::from([
            'source_id' => $lineItemId,
            'source_key' => $sourceKey,
        ]);
    }
}
