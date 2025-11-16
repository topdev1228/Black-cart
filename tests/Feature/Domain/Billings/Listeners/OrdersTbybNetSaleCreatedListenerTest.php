<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Billings\Listeners;

use App;
use App\Domain\Billings\Events\TbybNetSaleCreatedEvent;
use App\Domain\Billings\Listeners\OrdersTbybNetSaleCreatedListener;
use App\Domain\Billings\Values\OrdersTbybNetSaleCreatedEvent;
use App\Domain\Billings\Values\TbybNetSale as TbybNetSaleValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Event;
use Tests\TestCase;

class OrdersTbybNetSaleCreatedListenerTest extends TestCase
{
    protected Store $currentStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->currentStore));
    }

    /**
     * A basic feature test example.
     */
    public function testItCreatesBillingsTbybNetSale(): void
    {
        Event::fake([
            TbybNetSaleCreatedEvent::class,
        ]);

        $tbybNetSaleValue = TbybNetSaleValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $listener = resolve(OrdersTbybNetSaleCreatedListener::class);
        $listener->handle(new OrdersTbybNetSaleCreatedEvent($tbybNetSaleValue));

        Event::assertDispatched(
            TbybNetSaleCreatedEvent::class,
            function (TbybNetSaleCreatedEvent $event) use ($tbybNetSaleValue) {
                $this->assertEquals($event->tbybNetSale->storeId, $tbybNetSaleValue->storeId);
                $this->assertEquals($event->tbybNetSale->dateStart->toDateString(), $tbybNetSaleValue->dateStart->toDateString());
                $this->assertEquals($event->tbybNetSale->dateEnd->toDateString(), $tbybNetSaleValue->dateEnd->toDateString());
                $this->assertEqualsWithDelta($event->tbybNetSale->timeRangeStart, $tbybNetSaleValue->timeRangeStart, 1);
                $this->assertEqualsWithDelta($event->tbybNetSale->timeRangeEnd, $tbybNetSaleValue->timeRangeEnd, 1);
                $this->assertEquals($event->tbybNetSale->currency, $tbybNetSaleValue->currency);
                $this->assertEquals($event->tbybNetSale->tbybGrossSales, $tbybNetSaleValue->tbybGrossSales);
                $this->assertEquals($event->tbybNetSale->tbybDiscounts, $tbybNetSaleValue->tbybDiscounts);
                $this->assertEquals($event->tbybNetSale->tbybRefundedGrossSales, $tbybNetSaleValue->tbybRefundedGrossSales);
                $this->assertEquals($event->tbybNetSale->tbybRefundedDiscounts, $tbybNetSaleValue->tbybRefundedDiscounts);
                $this->assertEquals($event->tbybNetSale->tbybNetSales, $tbybNetSaleValue->tbybNetSales);

                return true;
            }
        );
    }
}
