<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Trials\Listeners;

use App\Domain\Trials\Enums\TrialStatus;
use App\Domain\Trials\Events\TrialGroupStartedEvent;
use App\Domain\Trials\Events\TrialStartedEvent;
use App\Domain\Trials\Listeners\LineItemCancelledListener;
use App\Domain\Trials\Models\Trialable;
use App\Domain\Trials\Repositories\TrialableRepository;
use App\Domain\Trials\Services\TrialService;
use App\Domain\Trials\Values\LineItemCancelledEvent;
use App\Domain\Trials\Values\Trialable as TrialableValue;
use Event;
use Str;
use Tests\TestCase;

class LineItemCancelledListenerTest extends TestCase
{
    protected TrialableRepository $trialableRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trialableRepository = resolve(TrialableRepository::class);
    }

    /**
     * A basic feature test example.
     */
    public function testLineItemCancelled(): void
    {
        $trialable = TrialableValue::from(Trialable::factory()->create([
            'source_key' => TrialService::TRIAL_SOURCE_KEY,
        ]));

        $listener = resolve(LineItemCancelledListener::class);
        $listener->handle(new LineItemCancelledEvent($trialable->sourceId));

        $trialable = $this->trialableRepository->getById($trialable->id);

        $this->assertEquals(TrialStatus::CANCELLED, $trialable->status);
    }

    public function testTrialStartsForRemainingItems(): void
    {
        Event::fake([
            TrialStartedEvent::class, TrialGroupStartedEvent::class,
        ]);
        $groupKey = Str::uuid();
        $toCancel = Trialable::factory()->create([
            'source_key' => TrialService::TRIAL_SOURCE_KEY,
            'group_key' => $groupKey,
        ]);

        $toTrial = Trialable::factory()->create([
            'source_key' => TrialService::TRIAL_SOURCE_KEY,
            'group_key' => $groupKey,
            'status' => TrialStatus::PRETRIAL,
        ]);

        $listener = resolve(LineItemCancelledListener::class);
        $listener->handle(new LineItemCancelledEvent($toCancel->source_id));

        $cancelledTrialable = $this->trialableRepository->getById($toCancel->id);
        $this->assertEquals(TrialStatus::CANCELLED, $cancelledTrialable->status);
        $inTrialTrialable = $this->trialableRepository->getById($toTrial->id);
        $this->assertEquals(TrialStatus::TRIAL, $inTrialTrialable->status);

        Event::assertDispatched(TrialStartedEvent::class);
        Event::assertDispatched(TrialGroupStartedEvent::class);
    }
}
