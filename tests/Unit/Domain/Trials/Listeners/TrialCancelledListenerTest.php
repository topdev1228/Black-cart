<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Trials\Listeners;

use App\Domain\Trials\Enums\TrialStatus;
use App\Domain\Trials\Listeners\LineItemCancelledListener;
use App\Domain\Trials\Models\Trialable as TrialModel;
use App\Domain\Trials\Repositories\TrialableRepository;
use App\Domain\Trials\Services\TrialService;
use App\Domain\Trials\Values\LineItemCancelledEvent;
use App\Domain\Trials\Values\Trialable as TrialValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class TrialCancelledListenerTest extends TestCase
{
    protected TrialService $trialService;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic feature test example.
     */
    public function testItCancelsTrial(): void
    {
        $trialable = TrialModel::factory()->create([
            'status' => TrialStatus::INIT,
            'source_key' => TrialService::TRIAL_SOURCE_KEY,
        ]);

        $trialValue = TrialValue::from($trialable);
        $listener = resolve(LineItemCancelledListener::class);

        $listener->handle(new LineItemCancelledEvent($trialValue->sourceId));

        $trialable->refresh();

        $this->assertEquals(TrialStatus::CANCELLED, $trialable->status);
    }

    /**
     * A basic feature test example.
     */
    public function testItIgnoresUntrialedItems(): void
    {
        $trialRepo = $this->mock(TrialableRepository::class);
        $trialRepo->shouldReceive('getBySource')->once()->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Programs\Models\Trialable]'));

        $listener = resolve(LineItemCancelledListener::class);

        $listener->handle(new LineItemCancelledEvent('123456'));
    }
}
