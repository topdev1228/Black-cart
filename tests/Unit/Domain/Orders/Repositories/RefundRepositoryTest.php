<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Repositories;

use App;
use App\Domain\Orders\Events\RefundCreatedEvent;
use App\Domain\Orders\Models\Refund;
use App\Domain\Orders\Repositories\RefundRepository;
use App\Domain\Orders\Values\Refund as RefundValue;
use App\Domain\Stores\Models\Store;
use Carbon\CarbonImmutable;
use Event;
use Tests\TestCase;

class RefundRepositoryTest extends TestCase
{
    protected Store $store;
    protected RefundRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create();
        App::context(store: $this->store);
        $this->repository = resolve(RefundRepository::class);
    }

    public function testItCreatesARefund(): void
    {
        Event::fake([
            RefundCreatedEvent::class,
        ]);

        $refundRepository = resolve(RefundRepository::class);

        $refund = RefundValue::builder()->create([
            'source_refund_reference_id' => 'gid://shopify/Refund/12345',
            'shop_currency' => 'USD',
            'customer_currency' => 'CAD',
        ]);

        $newRefund = $refundRepository->create(
            $refund
        );

        Event::assertDispatched(RefundCreatedEvent::class, function (RefundCreatedEvent $event) use ($newRefund) {
            $this->assertEquals($event->refund->id, $newRefund->id);

            return true;
        });

        $this->assertDatabaseHas('orders_refunds', [
            'source_refund_reference_id' => 'gid://shopify/Refund/12345',
            'order_id' => $refund->orderId,
            'shop_currency' => 'USD',
            'customer_currency' => 'CAD',
            'store_id' => $refund->storeId,
            'tbyb_gross_sales_shop_amount' => $refund->tbybGrossSalesShopAmount->getMinorAmount()->toInt(),
            'upfront_gross_sales_shop_amount' => $refund->upfrontGrossSalesShopAmount->getMinorAmount()->toInt(),
            'tbyb_discounts_shop_amount' => $refund->tbybDiscountsShopAmount->getMinorAmount()->toInt(),
            'upfront_discounts_shop_amount' => $refund->upfrontDiscountsShopAmount->getMinorAmount()->toInt(),
            'order_level_refund_shop_amount' => $refund->orderLevelRefundShopAmount->getMinorAmount()->toInt(),
            'tbyb_gross_sales_customer_amount' => $refund->tbybGrossSalesCustomerAmount->getMinorAmount()->toInt(),
            'upfront_gross_sales_customer_amount' => $refund->upfrontGrossSalesCustomerAmount->getMinorAmount()->toInt(),
            'tbyb_discounts_customer_amount' => $refund->tbybDiscountsCustomerAmount->getMinorAmount()->toInt(),
            'upfront_discounts_customer_amount' => $refund->upfrontDiscountsCustomerAmount->getMinorAmount()->toInt(),
            'order_level_refund_customer_amount' => $refund->orderLevelRefundCustomerAmount->getMinorAmount()->toInt(),
        ]);
    }

    public function testItGetsGrossSalesCorrectly(): void
    {
        $startDate = CarbonImmutable::now()->subMonths(6);

        Refund::withoutEvents(function () use ($startDate) {
            Refund::factory()->count(5)->create([
                'store_id' => $this->store->id,
                'tbyb_gross_sales_shop_amount' => 1541,
                'created_at' => CarbonImmutable::now()->subSecond(),
            ]);
            Refund::factory()->count(1)->create([
                'store_id' => $this->store->id,
                'tbyb_gross_sales_shop_amount' => 9999,
                'created_at' => $startDate->subDay(),
            ]);
            Refund::factory()->count(1)->create([
                'store_id' => $this->store->id,
                'tbyb_gross_sales_shop_amount' => 19999,
                'created_at' => $startDate->addDay(),
            ]);
            Refund::factory()->count(1)->create([
                'store_id' => 'different_store_id',
                'tbyb_gross_sales_shop_amount' => 19999,
                'created_at' => $startDate->addDay(),
            ]);
        });

        $return = $this->repository->getGrossSales(CarbonImmutable::now(), $startDate);
        $this->assertEquals(27704, $return);
    }

    public function testItGetsDiscountsCorrectly(): void
    {
        $startDate = CarbonImmutable::now()->subMonths(6);

        Refund::withoutEvents(function () use ($startDate) {
            Refund::factory()->count(5)->create([
                'store_id' => $this->store->id,
                'tbyb_discounts_shop_amount' => 1541,
                'created_at' => CarbonImmutable::now()->subSecond(),
            ]);
            Refund::factory()->count(1)->create([
                'store_id' => $this->store->id,
                'tbyb_discounts_shop_amount' => 9999,
                'created_at' => $startDate->subDay(),
            ]);
            Refund::factory()->count(1)->create([
                'store_id' => $this->store->id,
                'tbyb_discounts_shop_amount' => 19999,
                'created_at' => $startDate->addDay(),
            ]);

            Refund::factory()->count(1)->create([
                'store_id' => 'different_store_id',
                'tbyb_discounts_shop_amount' => 19999,
                'created_at' => $startDate->addDay(),
            ]);
        });

        $return = $this->repository->getDiscounts(CarbonImmutable::now(), $startDate);
        $this->assertEquals(27704, $return);
    }
}
