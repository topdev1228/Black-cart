<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Events\ReturnCreatedEvent;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Repositories\ReturnRepository;
use App\Domain\Orders\Services\ReturnService;
use App\Domain\Orders\Values\OrderReturn as OrderReturnValue;
use App\Domain\Orders\Values\WebhookReturnsApprove;
use App\Domain\Stores\Models\Store;
use Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Str;
use Tests\Fixtures\Domains\Orders\ReturnData;
use Tests\TestCase;

class ReturnServiceTest extends TestCase
{
    use ReturnData;

    protected ReturnService $service;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSave(): void
    {
        $repositoryMock = $this->mock(ReturnRepository::class);
        $this->service = resolve(ReturnService::class);

        $value = OrderReturnValue::builder()->create();
        $repositoryMock->shouldReceive('save')->andReturn($value);

        $return = $this->service->save($value);

        $this->assertEquals($value->id, $return->id);
    }

    public function testGetBySourceId(): void
    {
        $repositoryMock = $this->mock(ReturnRepository::class);
        $this->service = resolve(ReturnService::class);

        $value = OrderReturnValue::builder()->create([
            'source_id' => (string) Str::uuid(),
        ]);
        $repositoryMock->shouldReceive('getBySourceId')->andReturn($value);

        $return = $this->service->getBySourceId($value->sourceId);

        $this->assertEquals($value, $return);
    }

    #[DataProvider('returnDataProvider')]
    public function testItCreatesReturnFromWebhook(
        string $storeId,
        string $orderId,
        string $shopCurrency,
        string $customerCurrency,
        array $lineItems,
        array $webhookData,
        array $assertions
    ): void {
        $store = Store::factory()->create(['id' => $storeId, 'currency' => $shopCurrency]);
        App::context(store: $store);

        Event::fake([
            ReturnCreatedEvent::class,
        ]);

        $order = Order::withoutEvents(function () use ($store, $orderId, $shopCurrency, $customerCurrency) {
            return Order::factory()->create([
                'store_id' => $store->id,
                'source_id' => 'gid://shopify/Order/' . $orderId,
                'shop_currency' => $shopCurrency,
                'customer_currency' => $customerCurrency,
            ]);
        });

        $returnService = resolve(ReturnService::class);

        foreach ($lineItems as $lineItem) {
            $lineItem['source_id'] = Str::shopifyGid($lineItem['source_id'], 'LineItem');
            LineItem::factory()->create($lineItem);
        }

        $webhookReturnCreate = WebhookReturnsApprove::builder()->create($webhookData);
        $newReturn = $returnService->createFromWebhook($webhookReturnCreate);

        $this->assertDatabaseHas('orders_returns', [
            'source_id' => $webhookReturnCreate->adminGraphqlApiId,
            'order_id' => $order->id,
            'status' => $webhookReturnCreate->status,
            'name' => $webhookReturnCreate->name,
            'total_quantity' => $webhookReturnCreate->totalReturnLineItems,
            'tbyb_gross_sales_shop_amount' => $assertions['orders_returns']['tbyb_gross_sales_shop_amount'],
            'tbyb_gross_sales_customer_amount' => $assertions['orders_returns']['tbyb_gross_sales_customer_amount'],
            'upfront_gross_sales_shop_amount' => $assertions['orders_returns']['upfront_gross_sales_shop_amount'],
            'upfront_gross_sales_customer_amount' => $assertions['orders_returns']['upfront_gross_sales_customer_amount'],
            'tbyb_discounts_shop_amount' => $assertions['orders_returns']['tbyb_discounts_shop_amount'],
            'tbyb_discounts_customer_amount' => $assertions['orders_returns']['tbyb_discounts_customer_amount'],
            'upfront_discounts_shop_amount' => $assertions['orders_returns']['upfront_discounts_shop_amount'],
            'upfront_discounts_customer_amount' => $assertions['orders_returns']['upfront_discounts_customer_amount'],
            'tbyb_tax_shop_amount' => $assertions['orders_returns']['tbyb_tax_shop_amount'],
            'tbyb_tax_customer_amount' => $assertions['orders_returns']['tbyb_tax_customer_amount'],
            'upfront_tax_shop_amount' => $assertions['orders_returns']['upfront_tax_shop_amount'],
            'upfront_tax_customer_amount' => $assertions['orders_returns']['upfront_tax_customer_amount'],
            'tbyb_total_shop_amount' => $assertions['orders_returns']['tbyb_total_shop_amount'],
            'tbyb_total_customer_amount' => $assertions['orders_returns']['tbyb_total_customer_amount'],
        ]);

        foreach ($assertions['orders_returns_line_items'] as $key => $assertion) {
            $this->assertDatabaseHas('orders_returns_line_items', [
                'source_id' => $webhookReturnCreate->returnLineItems[$key]->adminGraphqlApiId,
                'quantity' => $webhookReturnCreate->returnLineItems[$key]->quantity,
                'line_item_id' => $assertion['line_item_id'],
                'gross_sales_shop_amount' => $assertion['gross_sales_shop_amount'],
                'gross_sales_customer_amount' => $assertion['gross_sales_customer_amount'],
                'discounts_shop_amount' => $assertion['discounts_shop_amount'],
                'discounts_customer_amount' => $assertion['discounts_customer_amount'],
                'tax_shop_amount' => $assertion['tax_shop_amount'],
                'tax_customer_amount' => $assertion['tax_customer_amount'],
                'is_tbyb' => $assertion['is_tbyb'],
            ]);
        }

        Event::assertDispatched(ReturnCreatedEvent::class, function (ReturnCreatedEvent $event) use ($newReturn) {
            $this->assertEquals($event->return->id, $newReturn->id);
            $this->assertEquals($event->return->sourceId, $newReturn->sourceId);

            return true;
        });
    }
}
