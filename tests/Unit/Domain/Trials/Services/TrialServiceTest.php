<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Trials\Services;

use App\Domain\Trials\Enums\TrialStatus;
use App\Domain\Trials\Events\TrialExpiredEvent;
use App\Domain\Trials\Events\TrialGroupExpiredEvent;
use App\Domain\Trials\Events\TrialGroupStartedEvent;
use App\Domain\Trials\Events\TrialStartedEvent;
use App\Domain\Trials\Models\Trialable as TrialableModel;
use App\Domain\Trials\Services\TrialService;
use App\Domain\Trials\Values\Program;
use App\Domain\Trials\Values\Trialable as TrialableValue;
use Event;
use Http;
use Illuminate\Support\Facades\Date;
use Str;
use Tests\TestCase;

class TrialServiceTest extends TestCase
{
    const TEST_CONDITION = 'test-condition';
    const TEST_SOURCE_KEY = 'test-source-key';

    protected TrialService $trialService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trialService = resolve(TrialService::class);
        Event::fake();
    }

    public function testItUpdatesCondition(): void
    {
        $trialModel = TrialableModel::factory()->create([
            'status' => TrialStatus::INIT,
        ]);

        $trialValue = TrialableValue::from($trialModel);
        $this->trialService->updateCondition($trialValue, self::TEST_CONDITION);

        $trialModel = $trialModel->refresh();
        $this->assertNotEquals(TrialStatus::INIT, $trialModel->status);
    }

    public function testItMovesSingleToTrial(): void
    {
        $trialModel = TrialableModel::factory()->create([
            'status' => TrialStatus::INIT,
        ]);

        $trialValue = TrialableValue::from($trialModel);
        $this->trialService->updateCondition($trialValue, self::TEST_CONDITION);

        $trialModel = $trialModel->refresh();
        $this->assertEquals(TrialStatus::TRIAL, $trialModel->status);
        Event::assertDispatched(TrialStartedEvent::class);
        Event::assertNotDispatched(TrialGroupStartedEvent::class);
    }

    public function testItMovesGroupToTrial(): void
    {
        $trialModels = TrialableModel::factory()->count(2)->create([
            'status' => TrialStatus::INIT,
            'source_key' => self::TEST_SOURCE_KEY,
            'group_key' => '123456',
        ]);

        $trialModel1 = $trialModels->shift();
        $trialModel2 = $trialModels->shift();

        $trialValue1 = TrialableValue::from($trialModel1);
        $trialValue2 = TrialableValue::from($trialModel2);
        $this->trialService->updateCondition($trialValue1, self::TEST_CONDITION);
        $this->trialService->updateCondition($trialValue2, self::TEST_CONDITION);

        $trialModel1 = $trialModel1->refresh();
        $trialModel2 = $trialModel1->refresh();

        $this->assertEquals(TrialStatus::TRIAL, $trialModel1->status);
        $this->assertEquals(TrialStatus::TRIAL, $trialModel2->status);
        Event::assertDispatched(TrialGroupStartedEvent::class);
    }

    public function testItDoesntMoveGroupToTrial(): void
    {
        $trialModels = TrialableModel::factory()->count(3)->create([
            'status' => TrialStatus::INIT,
            'source_key' => self::TEST_SOURCE_KEY,
            'group_key' => '123456',
        ]);

        $trialModel1 = $trialModels->shift();
        $trialModel2 = $trialModels->shift();
        $trialModel3 = $trialModels->shift();

        $trialValue = TrialableValue::from($trialModel1);
        $this->trialService->updateCondition($trialValue, self::TEST_CONDITION);

        $trialModel1 = $trialModel1->refresh();
        $this->assertEquals(TrialStatus::PRETRIAL, $trialModel1->status);
        $this->assertEquals(TrialStatus::INIT, $trialModel2->status);
        $this->assertEquals(TrialStatus::INIT, $trialModel3->status);
        Event::assertNotDispatched(TrialGroupStartedEvent::class);
    }

    public function testItExpiresOneTrial(): void
    {
        $trialable = TrialableModel::factory()->create([
            'status' => TrialStatus::TRIAL,
        ]);

        $this->trialService->expireTrial(TrialableValue::from($trialable));

        $this->assertEquals(TrialStatus::COMPLETE, $trialable->refresh()->status);
        Event::assertDispatched(TrialExpiredEvent::class);
    }

    public function testItDoesntExpireTrialsPreTrial(): void
    {
        $trialable1 = TrialableModel::factory()->create([
            'status' => TrialStatus::INIT,
        ]);
        $trialable2 = TrialableModel::factory()->create([
            'status' => TrialStatus::PRETRIAL,
        ]);
        $this->trialService->expireTrial(TrialableValue::from($trialable1));
        $this->trialService->expireTrial(TrialableValue::from($trialable2));

        $this->assertEquals(TrialStatus::INIT, $trialable1->refresh()->status);
        $this->assertEquals(TrialStatus::PRETRIAL, $trialable2->refresh()->status);

        Event::assertNotDispatched(TrialExpiredEvent::class);
    }

    public function testItExpiresTrialGroup(): void
    {
        $trialables = TrialableModel::factory()->count(3)->create([
            'status' => TrialStatus::TRIAL,
            'group_key' => (string) Str::uuid(),
        ]);

        foreach ($trialables as $trialable) {
            $this->trialService->expireTrial(TrialableValue::from($trialable));
            $this->assertEquals(TrialStatus::COMPLETE, $trialable->refresh()->status);
        }

        Event::assertDispatched(TrialGroupExpiredEvent::class);
    }

    public function testCalculateExpiryDate(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => 12345,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'USD',
                        'dropOffDays' => '7',
                    ],
                ],
            ]),
        ]);
        $trialable = TrialableValue::from(TrialableModel::factory()->create([
            'status' => TrialStatus::TRIAL,
            'group_key' => (string) Str::uuid(),
        ]));
        Program::builder()->create();
        $response = $this->trialService->calculateExpiryTime($trialable);
        $this->assertEquals(Date::now()->addDays($trialable->trialDuration)->addDays(7)->toDateString(), $response->toDateString());
    }
}
