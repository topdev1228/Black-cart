<?php
declare(strict_types=1);

namespace Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\FetchAndSaveTransactionsAfterOrderCreatedListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\OrderCreatedEvent as OrderCreatedEventValue;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryServerException;
use App\Domain\Stores\Models\Store;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyGetTransactionsByOrderIdResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class FetchAndSaveTransactionsAfterOrderCreatedListenerTest extends TestCase
{
    use ShopifyGetTransactionsByOrderIdResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->store);
    }

    #[DataProvider('shopifyErrorExceptionsProvider')]
    public function testItDoesNotFetchAndSaveTransactionsOnShopifyErrors(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->store->id]);
        });
        $orderValue = OrderValue::from($order);

        $event = new OrderCreatedEventValue($orderValue);
        $listener = resolve(FetchAndSaveTransactionsAfterOrderCreatedListener::class);
        $listener->handle($event);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotFetchAndSaveTransactionOnShopifyOrderIdError(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetTransactionsByOrderIdErrorResponse()),
        ]);
        $this->expectException(ShopifyQueryServerException::class);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $event = new OrderCreatedEventValue($orderValue);
        $listener = resolve(FetchAndSaveTransactionsAfterOrderCreatedListener::class);
        $transactions = $listener->handle($event);

        $this->assertCount(0, $transactions);
        $this->assertDatabaseCount('orders_transactions', 0);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotFetchAndSaveTransactionOnShopifyOrderNotFound(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetTransactionsByOrderIdOrderNotFoundResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $event = new OrderCreatedEventValue($orderValue);
        $listener = resolve(FetchAndSaveTransactionsAfterOrderCreatedListener::class);
        $transactions = $listener->handle($event);

        $this->assertCount(0, $transactions);
        $this->assertDatabaseCount('orders_transactions', 0);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItFetchesAndButDoesNotSaveExistingTransactions(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetTransactionsByOrderIdSuccessResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->count(2)
                ->state(new Sequence(
                    ['source_id' => 'gid://shopify/OrderTransaction/5472548814977'],
                    ['source_id' => 'gid://shopify/OrderTransaction/5472550453377'],
                ))->create([
                    'store_id' => $this->store->id,
                    'order_id' => $order->id,
                    'created_at' => '2023-01-01 00:00:00',
                    'updated_at' => '2023-01-01 00:00:00', // verify that updated timestamp didn't change
                ]);
        });
        $this->assertDatabaseCount('orders_transactions', 2);

        $event = new OrderCreatedEventValue($orderValue);
        $listener = resolve(FetchAndSaveTransactionsAfterOrderCreatedListener::class);
        $transactions = $listener->handle($event);

        $this->assertCount(2, $transactions);
        $this->assertDatabaseCount('orders_transactions', 2);
        foreach ($transactions as $transaction) {
            $this->assertEquals($order->id, $transaction->orderId);

            $actualTransaction = $transaction->toArray();
            $actualTransaction['test'] = intval($actualTransaction['test']);
            $actualTransaction['processed_at'] = Date::parse($actualTransaction['processed_at'])->format('Y-m-d H:i:s');

            // Don't care about these fields
            foreach (['transaction_data'] as $ignoreField) {
                unset($actualTransaction[$ignoreField]);
            }

            $this->assertDatabaseHas('orders_transactions', $actualTransaction);
        }

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItFetchesAndSavesTransactions(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetTransactionsByOrderIdSuccessResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $event = new OrderCreatedEventValue($orderValue);
        $listener = resolve(FetchAndSaveTransactionsAfterOrderCreatedListener::class);
        $transactions = $listener->handle($event);

        $this->assertCount(2, $transactions);
        $this->assertDatabaseCount('orders_transactions', 2);
        foreach ($transactions as $transaction) {
            $this->assertEquals($order->id, $transaction->orderId);

            $actualTransaction = $transaction->toArray();
            $actualTransaction['test'] = intval($actualTransaction['test']);
            $actualTransaction['processed_at'] = Date::parse($actualTransaction['processed_at'])->format('Y-m-d H:i:s');

            // Don't care about these fields
            foreach (['transaction_data'] as $ignoreField) {
                unset($actualTransaction[$ignoreField]);
            }

            $this->assertDatabaseHas('orders_transactions', $actualTransaction);
        }

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItFetchesAndSavesCaptureTransaction(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetCaptureTransactionsByOrderIdSuccessResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $event = new OrderCreatedEventValue($orderValue);
        $listener = resolve(FetchAndSaveTransactionsAfterOrderCreatedListener::class);
        $transactions = $listener->handle($event);

        $this->assertCount(2, $transactions);
        $this->assertDatabaseCount('orders_transactions', 2);
        foreach ($transactions as $transaction) {
            $this->assertEquals($order->id, $transaction->orderId);

            $actualTransaction = $transaction->toArray();
            $actualTransaction['test'] = intval($actualTransaction['test']);
            $actualTransaction['processed_at'] = Date::parse($actualTransaction['processed_at'])->format('Y-m-d H:i:s');
            if (!empty($actualTransaction['authorization_expires_at'])) {
                $actualTransaction['authorization_expires_at'] = Date::parse($actualTransaction['authorization_expires_at'])->format('Y-m-d H:i:s');
            }

            // Don't care about these fields
            foreach (['transaction_data'] as $ignoreField) {
                unset($actualTransaction[$ignoreField]);
            }

            $this->assertDatabaseHas('orders_transactions', $actualTransaction);
        }

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
