<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Trials\Listeners;

use App\Domain\Trials\Events\TrialStartedEvent;
use App\Domain\Trials\Jobs\ExpireTrialJob;
use App\Domain\Trials\Listeners\TrialStartedListener;
use App\Domain\Trials\Services\TrialService;
use App\Domain\Trials\Values\Program;
use App\Domain\Trials\Values\Trialable;
use Illuminate\Support\Facades\Date;
use Queue;
use Tests\TestCase;

class TrialStartedListenerTest extends TestCase
{
    protected TrialService $trialService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trialService = $this->mock(TrialService::class);
        Program::builder()->create();
    }

    /**
     * A basic feature test example.
     */
    public function testItDispatchesExpiryJob(): void
    {
        Queue::fake();

        $trialable = Trialable::builder()->create([
            'tryPeriodDays' => 0,
        ]);
        $this->trialService->shouldReceive('calculateExpiryTime')->once()->andReturn(Date::now());

        $listener = new TrialStartedListener($this->trialService);
        $listener->handle(new TrialStartedEvent($trialable));

        Queue::assertPushed(ExpireTrialJob::class);
    }

    /**
     * A basic feature test example.
     */
    public function testItExpiresTrial(): void
    {
        $trialable = Trialable::builder()->create([
            'tryPeriodDays' => 0,
        ]);

        $this->trialService->shouldReceive('expireTrial')->once();
        $this->trialService->shouldReceive('calculateExpiryTime')->once()->andReturn(Date::now());

        $listener = new TrialStartedListener($this->trialService);
        $listener->handle(new TrialStartedEvent($trialable));
    }
}
