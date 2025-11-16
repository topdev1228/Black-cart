<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Trials\Listeners;

use App\Domain\Trials\Enums\TrialStatus;
use App\Domain\Trials\Listeners\TrialableDeliveredListener;
use App\Domain\Trials\Models\Trialable;
use App\Domain\Trials\Values\Trialable as TrialableValue;
use App\Domain\Trials\Values\TrialableDeliveredEvent;
use Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class TrialableDeliveredListenerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic feature test example.
     */
    public function testTrialStarts(): void
    {
        Event::fake();

        /**
         * @var Trialable $trial
         */
        $trial = Trialable::factory()->create();
        $eventTrial = TrialableValue::from([
            'source_id' => $trial->source_id,
            'source_key' => $trial->source_key,
        ]);

        $listener = resolve(TrialableDeliveredListener::class);
        $listener->handle(new TrialableDeliveredEvent(TrialableValue::from($eventTrial)));

        $this->assertEquals(TrialStatus::TRIAL, $trial->refresh()->status);
    }

    /**
     * A basic feature test example.
     */
    public function testTrialStartsForGroup(): void
    {
        Event::fake();

        $sourceKey = Str::uuid();
        $groupKey = Str::uuid();
        $trials = Trialable::factory()->count(2)->create([
            'source_key' => $sourceKey,
            'group_key' => $groupKey,
        ]);

        $firstTrial = $trials->pop();
        $secondTrial = $trials->pop();
        $secondTrial->update([
            'status' => TrialStatus::PRETRIAL,
        ]);

        $listener = resolve(TrialableDeliveredListener::class);
        $listener->handle(new TrialableDeliveredEvent(TrialableValue::from($firstTrial)));

        $this->assertEquals(TrialStatus::TRIAL, $firstTrial->refresh()->status);
        $this->assertEquals(TrialStatus::TRIAL, $secondTrial->refresh()->status);
    }

    public function testTrialDoesntStartOnGroupNotReady(): void
    {
        Event::fake();

        $sourceKey = Str::uuid();
        $groupKey = Str::uuid();
        $trials = Trialable::factory()->count(2)->create([
            'source_key' => $sourceKey,
            'group_key' => $groupKey,
        ]);

        $firstTrial = $trials->pop();
        $secondTrial = $trials->pop();

        $listener = resolve(TrialableDeliveredListener::class);
        $listener->handle(new TrialableDeliveredEvent(TrialableValue::from($firstTrial)));

        $this->assertEquals(TrialStatus::PRETRIAL, $firstTrial->refresh()->status);
        $this->assertEquals(TrialStatus::INIT, $secondTrial->refresh()->status);
    }

    public function testTrialStartIgnoresCancelledItem(): void
    {
        Event::fake();

        $sourceKey = Str::uuid();
        $groupKey = Str::uuid();
        $trials = Trialable::factory()->count(2)->create([
            'source_key' => $sourceKey,
            'group_key' => $groupKey,
        ]);

        $firstTrial = $trials->pop();
        $secondTrial = $trials->pop();
        $secondTrial->update([
            'status' => TrialStatus::CANCELLED,
        ]);

        $listener = resolve(TrialableDeliveredListener::class);
        $listener->handle(new TrialableDeliveredEvent(TrialableValue::from($firstTrial)));

        $this->assertEquals(TrialStatus::TRIAL, $firstTrial->refresh()->status);
        $this->assertEquals(TrialStatus::CANCELLED, $secondTrial->refresh()->status);
    }
}
