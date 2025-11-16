<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Events;

use App\Domain\Shared\Traits\Broadcastable;
use App\Domain\Shopify\Models\Job;
use App\Domain\Shopify\Values\Job as JobValue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(Job $job)
 */
class JobUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;
    use Broadcastable;

    public JobValue $job;

    /**
     * Create a new event instance.
     */
    public function __construct(public Job $jobModel)
    {
        $this->job = JobValue::from($this->jobModel);
    }
}
