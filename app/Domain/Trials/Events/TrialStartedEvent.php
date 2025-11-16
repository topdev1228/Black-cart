<?php
declare(strict_types=1);

namespace App\Domain\Trials\Events;

use App\Domain\Trials\Values\Trialable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrialStartedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Trialable $trialable)
    {
    }
}
