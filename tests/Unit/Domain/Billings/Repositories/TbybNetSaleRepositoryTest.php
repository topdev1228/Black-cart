<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Billings\Repositories;

use App;
use App\Domain\Billings\Events\TbybNetSaleCreatedEvent;
use App\Domain\Billings\Models\TbybNetSale;
use App\Domain\Billings\Repositories\TbybNetSaleRepository;
use App\Domain\Billings\Values\TbybNetSale as TbybNetSaleValue;
use App\Domain\Stores\Models\Store;
use Event;
use Tests\TestCase;

class TbybNetSaleRepositoryTest extends TestCase
{
    protected Store $currentStore;
    protected TbybNetSaleRepository $tbybNetSaleRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);

        $this->tbybNetSaleRepository = resolve(TbybNetSaleRepository::class);
    }

    public function testItDoesNotCreatesTbybNetSaleOnExisting(): void
    {
        Event::fake([
            TbybNetSaleCreatedEvent::class,
        ]);

        $tbybNetSale = TbybNetSale::withoutEvents(function () {
            return TbybNetSale::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'store_id' => $tbybNetSale->store_id,
            'time_range_start' => $tbybNetSale->time_range_start,
            'time_range_end' => $tbybNetSale->time_range_end,
        ]);

        $actualTbybNetSale = $this->tbybNetSaleRepository->store($tbybNetSaleValue);

        $this->assertEquals($tbybNetSale->id, $actualTbybNetSale->id);

        Event::assertNotDispatched(TbybNetSaleCreatedEvent::class);
    }

    public function testItCreatesTbybNetSale(): void
    {
        Event::fake([
            TbybNetSaleCreatedEvent::class,
        ]);

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $actualTbybNetSale = $this->tbybNetSaleRepository->store($tbybNetSaleValue);

        $this->assertNotEmpty($actualTbybNetSale->id);
        $this->assertEquals($tbybNetSaleValue->storeId, $actualTbybNetSale->storeId);
        $this->assertEquals($tbybNetSaleValue->dateStart->toDateString(), $actualTbybNetSale->dateStart->toDateString());
        $this->assertEquals($tbybNetSaleValue->dateEnd->toDateString(), $actualTbybNetSale->dateEnd->toDateString());
        $this->assertEqualsWithDelta($tbybNetSaleValue->timeRangeStart, $actualTbybNetSale->timeRangeStart, 1);
        $this->assertEqualsWithDelta($tbybNetSaleValue->timeRangeEnd, $actualTbybNetSale->timeRangeEnd, 1);
        $this->assertEquals($tbybNetSaleValue->currency, $actualTbybNetSale->currency);
        $this->assertEquals($tbybNetSaleValue->tbybGrossSales, $actualTbybNetSale->tbybGrossSales);
        $this->assertEquals($tbybNetSaleValue->tbybDiscounts, $actualTbybNetSale->tbybDiscounts);
        $this->assertEquals($tbybNetSaleValue->tbybRefundedGrossSales, $actualTbybNetSale->tbybRefundedGrossSales);
        $this->assertEquals($tbybNetSaleValue->tbybRefundedDiscounts, $actualTbybNetSale->tbybRefundedDiscounts);
        $this->assertEquals($tbybNetSaleValue->tbybNetSales, $actualTbybNetSale->tbybNetSales);

        Event::assertDispatched(
            TbybNetSaleCreatedEvent::class,
            function (TbybNetSaleCreatedEvent $event) use ($actualTbybNetSale) {
                $this->assertEquals($event->tbybNetSale->id, $actualTbybNetSale->id);

                return true;
            },
        );
    }
}
