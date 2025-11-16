<?php
declare(strict_types=1);

namespace App\Domain\Trials\Jobs;

use App\Domain\Trials\Services\TrialService;
use App\Domain\Trials\Values\Trialable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * @method static PendingDispatch dispatch(Trialable $trialable)
 * @method static PendingDispatch dispatchSync(Trialable $trialable)
 */
class ExpireTrialJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Trialable $trialable)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(TrialService $trialService): void
    {
        $trialService->expireTrial($this->trialable);
    }
}
