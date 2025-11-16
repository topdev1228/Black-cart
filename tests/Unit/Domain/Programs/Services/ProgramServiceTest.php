<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Programs\Services;

use App;
use App\Domain\Programs\Enums\DepositType;
use App\Domain\Programs\Events\ProgramSavedEvent;
use App\Domain\Programs\Models\Program;
use App\Domain\Programs\Repositories\ProgramRepository;
use App\Domain\Programs\Services\ProgramService;
use App\Domain\Programs\Services\ShopifyProgramService;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Stores\Models\Store;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Programs\Traits\ProgramConfigurationsTestData;
use Tests\Fixtures\Domains\Programs\Traits\ShopifySellingPlanGroupResponsesTestData;
use Tests\TestCase;

class ProgramServiceTest extends TestCase
{
    use ProgramConfigurationsTestData;
    use ShopifySellingPlanGroupResponsesTestData;

    protected Store $currentStore;
    protected ProgramService $programService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);

        $this->programService = resolve(ProgramService::class);
    }

    public function testItGetsAll(): void
    {
        Program::factory()->count(5)->create(['store_id' => $this->currentStore->id]);

        $programsActual = $this->programService->all();

        /**
         * Currently we only support one program, so we only return the last created program.
         */
        $this->assertEquals(1, $programsActual->count());
    }

    public function testItGetsProgramById(): void
    {
        $program = Program::factory()->create(['store_id' => $this->currentStore->id]);

        $programActual = $this->programService->getById($program->id);
        $this->assertEquals($program->id, $programActual->id);
    }

    #[DataProvider('programConfigurationsProvider')]
    public function testItCreatesProgram(
        int $tryPeriodDays,
        DepositType $depositType,
        int $depositValue,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupCreateSuccessResponse()),
        ]);

        Event::fake([
            ProgramSavedEvent::class,
        ]);

        $expectedProgramValue = ProgramValue::builder()->withShopifySellingPlanIds()->create([
            'store_id' => $this->currentStore->id,
            'try_period_days' => $tryPeriodDays,
            'deposit_type' => $depositType,
            'deposit_value' => $depositValue,
            'currency' => $this->currentStore->currency,
        ]);

        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'try_period_days' => $tryPeriodDays,
            'deposit_type' => $depositType,
            'deposit_value' => $depositValue,
            'currency' => $this->currentStore->currency,
        ]);

        $programActual = $this->programService->create($programValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        $this->assertNotEmpty($programActual->id);
        $this->assertEquals($expectedProgramValue->storeId, $programActual->storeId);
        $this->assertEquals($expectedProgramValue->shopifySellingPlanGroupId, $programActual->shopifySellingPlanGroupId);
        $this->assertEquals($expectedProgramValue->shopifySellingPlanId, $programActual->shopifySellingPlanId);
        $this->assertEquals($expectedProgramValue->tryPeriodDays, $programActual->tryPeriodDays);
        $this->assertEquals($expectedProgramValue->depositType, $programActual->depositType);
        $this->assertEquals($expectedProgramValue->depositValue, $programActual->depositValue);
        $this->assertEquals($expectedProgramValue->currency, $programActual->currency);
        $this->assertEquals($expectedProgramValue->minTbybItems, $programActual->minTbybItems);
        $this->assertEquals($expectedProgramValue->maxTbybItems, $programActual->maxTbybItems);

        Event::assertDispatched(ProgramSavedEvent::class);
    }

    public function testItDoesNotUpdatesNonExistentProgram(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [App\Domain\Programs\Models\Program] non_existent_program_id');

        $this->programService->update(
            'non_existent_program_id',
            ['name' => 'New Name', 'min_tbyb_items' => 10],
        );
    }

    public function testItDoesNotUpdateUnchangedProgram(): void
    {
        $this->partialMock(ProgramRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });
        $this->partialMock(ShopifyProgramService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('updateTbybProgram');
        });

        Event::fake([
            ProgramSavedEvent::class,
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create([
                'store_id' => $this->currentStore->id,
                'name' => 'Blackcart ABC Try',
                'currency' => $this->currentStore->currency,
                'min_tbyb_items' => 10,
            ]);
        });

        $programActual = $this->programService->update(
            $program->id,
            ['name' => $program->name, 'min_tbyb_items' => $program->min_tbyb_items],
        );

        $this->assertEquals($program->name, $programActual->name);
        $this->assertEquals($program->min_tbyb_items, $programActual->minTbybItems);

        Event::assertNotDispatched(ProgramSavedEvent::class);
    }

    #[DataProvider('programConfigurationsProvider')]
    public function testItUpdatesProgram(
        int $tryPeriodDays,
        DepositType $depositType,
        int $depositValue,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupUpdateSuccessResponse()),
        ]);

        Event::fake([
            ProgramSavedEvent::class,
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create([
                'store_id' => $this->currentStore->id,
                'currency' => $this->currentStore->currency,
            ]);
        });

        $programActual = $this->programService->update(
            $program->id,
            [
                'try_period_days' => $tryPeriodDays,
                'deposit_type' => $depositType->value,
                'deposit_value' => $depositValue,
            ],
        );

        $this->assertEquals($tryPeriodDays, $programActual->tryPeriodDays);
        $this->assertEquals($depositType, $programActual->depositType);
        $this->assertEquals($depositValue, $programActual->depositValue);

        $this->assertEquals($program->store_id, $programActual->storeId);
        $this->assertEquals($program->shopify_selling_plan_group_id, $programActual->shopifySellingPlanGroupId);
        $this->assertEquals($program->shopify_selling_plan_id, $programActual->shopifySellingPlanId);
        $this->assertEquals($program->currency, $programActual->currency);
        $this->assertEquals($program->min_tbyb_items, $programActual->minTbybItems);
        $this->assertEquals($program->max_tbyb_items, $programActual->maxTbybItems);

        Event::assertDispatched(ProgramSavedEvent::class);
    }
}
