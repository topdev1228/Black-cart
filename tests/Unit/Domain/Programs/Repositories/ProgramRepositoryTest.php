<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Programs\Repositories;

use App;
use App\Domain\Programs\Events\ProgramSavedEvent;
use App\Domain\Programs\Models\Program;
use App\Domain\Programs\Repositories\ProgramRepository;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Stores\Models\Store;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class ProgramRepositoryTest extends TestCase
{
    protected Store $currentStore;
    protected ProgramRepository $programRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);

        $this->programRepository = resolve(ProgramRepository::class);
    }

    public function testItGetsAllPrograms(): void
    {
        Program::factory()->count(5)->create(['store_id' => $this->currentStore->id]);

        /**
         * Currently we only support one program, so we only return the last created program.
         */
        $this->assertEquals(1, $this->programRepository->all()->count());
    }

    public function testItGetsProgramById(): void
    {
        $program = Program::factory()->create(['store_id' => $this->currentStore->id]);

        $programActual = $this->programRepository->getById($program->id);
        $this->assertEquals($program->id, $programActual->id);
        $this->assertEquals($program->name, $programActual->name);
    }

    public function testItCreatesProgram(): void
    {
        Event::fake([
            ProgramSavedEvent::class,
        ]);

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        $programActual = $this->programRepository->store($programValue);

        $this->assertNotEmpty($programActual->id);
        $this->assertEquals($programValue->storeId, $programActual->storeId);
        $this->assertEquals($programValue->shopifySellingPlanGroupId, $programActual->shopifySellingPlanGroupId);
        $this->assertEquals($programValue->shopifySellingPlanId, $programActual->shopifySellingPlanId);
        $this->assertEquals($programValue->tryPeriodDays, $programActual->tryPeriodDays);
        $this->assertEquals($programValue->depositType, $programActual->depositType);
        $this->assertEquals($programValue->depositValue, $programActual->depositValue);
        $this->assertEquals($programValue->currency, $programActual->currency);
        $this->assertEquals($programValue->minTbybItems, $programActual->minTbybItems);
        $this->assertEquals($programValue->maxTbybItems, $programActual->maxTbybItems);

        Event::assertDispatched(ProgramSavedEvent::class);
    }

    public function testItDoesNotUpdatesNonExistentProgram(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [App\Domain\Programs\Models\Program] non_existent_program_id');

        $this->programRepository->update(
            'non_existent_program_id',
            ['name' => 'New Name', 'min_tbyb_items' => 10],
        );
    }

    public function testItUpdatesProgram(): void
    {
        Event::fake([
            ProgramSavedEvent::class,
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->create(['store_id' => $this->currentStore->id]);
        });

        $programActual = $this->programRepository->update(
            $program->id,
            ['name' => 'New Name', 'min_tbyb_items' => 10],
        );

        $this->assertEquals('New Name', $programActual->name);
        $this->assertEquals(10, $programActual->minTbybItems);

        Event::assertDispatched(ProgramSavedEvent::class);
    }
}
