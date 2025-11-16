<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Events\TransactionCreatedEvent;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Services\TransactionService;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\Transaction as TransactionValue;
use App\Domain\Orders\Values\WebhookOrderTransactionsCreate;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryServerException;
use App\Domain\Stores\Models\Store;
use Carbon\CarbonImmutable;
use Event;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyGetTransactionResponsesTestData;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyGetTransactionsByOrderIdResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use ShopifyErrorsTestData;
    use ShopifyGetTransactionResponsesTestData;
    use ShopifyGetTransactionsByOrderIdResponsesTestData;

    protected Store $currentStore;
    protected TransactionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);
        $this->service = resolve(TransactionService::class);
    }

    public function testItDoesNotCreatesTransactionFromWebhookOnExistingTransaction(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $webhook = WebhookOrderTransactionsCreate::from(
            $this->loadFixtureData('order-transactions-create-authorization-webhook.json', 'Orders')
        );
        $sourceOrderId = sprintf('gid://shopify/Order/%d', $webhook->orderId);

        $order = Order::withoutEvents(function () use ($sourceOrderId) {
            return Order::factory()->create([
                'source_id' => $sourceOrderId,
                'store_id' => $this->currentStore->id,
                'shop_currency' => 'CAD',
                'customer_currency' => 'USD',
            ]);
        });
        $orderValue = OrderValue::from($order);

        $existingTransaction = Transaction::withoutEvents(function () use ($order, $webhook) {
            return Transaction::factory()->create([
                'order_id' => $order->id,
                'order_name' => $order->name,
                'source_order_id' => $order->source_id,
                'source_id' => $webhook->adminGraphqlApiId,
                'store_id' => $this->currentStore->id,
                'kind' => TransactionKind::AUTHORIZATION->value,
                'authorization_expires_at' => '2024-03-13 21:52:55',
                'parent_transaction_id' => null,
                'parent_transaction_source_id' => null,
                'shop_currency' => 'CAD',
                'shop_amount' => 69838,
                'customer_currency' => 'USD',
                'customer_amount' => 51680,
            ]);
        });

        Http::fake([
            \Illuminate\Support\Facades\App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getAuthorizationTransactionSuccessResponse()),
        ]);

        $this->assertDatabaseCount('orders_transactions', 1);

        $transactionValue = $this->service->createFromWebhook($webhook, $orderValue);

        $this->assertEquals($existingTransaction->id, $transactionValue->id);
        $this->assertDatabaseCount('orders_transactions', 1);
        $this->assertDatabaseHas('orders_transactions', [
            'id' => $existingTransaction->id,
            'source_id' => $webhook->adminGraphqlApiId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $sourceOrderId,
            'store_id' => $this->currentStore->id,
            'kind' => TransactionKind::AUTHORIZATION->value,
            'authorization_expires_at' => '2024-03-13 21:52:55',
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => null,
            'shop_currency' => 'CAD',
            'shop_amount' => 69838,
            'customer_currency' => 'USD',
            'customer_amount' => 51680,
            'transaction_source_name' => 'web',
        ]);

        Event::assertNotDispatched(TransactionCreatedEvent::class);
    }

    public function testItCreatesAuthorizationTransactionFromWebhook(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $webhook = WebhookOrderTransactionsCreate::from(
            $this->loadFixtureData('order-transactions-create-authorization-webhook.json', 'Orders')
        );
        $sourceOrderId = sprintf('gid://shopify/Order/%d', $webhook->orderId);

        $order = Order::withoutEvents(function () use ($sourceOrderId) {
            return Order::factory()->create([
                'source_id' => $sourceOrderId,
                'store_id' => $this->currentStore->id,
                'shop_currency' => 'CAD',
                'customer_currency' => 'USD',
            ]);
        });
        $orderValue = OrderValue::from($order);

        Http::fake([
            \Illuminate\Support\Facades\App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getAuthorizationTransactionSuccessResponse()),
        ]);

        $transactionValue = $this->service->createFromWebhook($webhook, $orderValue);

        $this->assertNotEmpty($transactionValue->id);
        $this->assertDatabaseHas('orders_transactions', [
            'id' => $transactionValue->id,
            'source_id' => $webhook->adminGraphqlApiId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $sourceOrderId,
            'store_id' => $this->currentStore->id,
            'kind' => TransactionKind::AUTHORIZATION->value,
            'authorization_expires_at' => '2024-03-13 21:52:55',
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => null,
            'shop_currency' => 'CAD',
            'shop_amount' => 69838,
            'customer_currency' => 'USD',
            'customer_amount' => 51680,
            'transaction_source_name' => 'web',
        ]);

        Event::assertDispatched(
            TransactionCreatedEvent::class,
            function (TransactionCreatedEvent $event) use ($transactionValue) {
                $this->assertEquals($transactionValue->id, $event->transaction->id);

                return true;
            }
        );
    }

    public function testItCreatesAuthorizationTransactionFromWebhookNullAuthorizationExpiresAtNullProcessedAtTimestamps(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 9, 12, 1, 1));

        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $webhook = WebhookOrderTransactionsCreate::from(
            $this->loadFixtureData('order-transactions-create-authorization-webhook.json', 'Orders')
        );
        $sourceOrderId = sprintf('gid://shopify/Order/%d', $webhook->orderId);

        $order = Order::withoutEvents(function () use ($sourceOrderId) {
            return Order::factory()->create([
                'source_id' => $sourceOrderId,
                'store_id' => $this->currentStore->id,
                'shop_currency' => 'CAD',
                'customer_currency' => 'USD',
            ]);
        });
        $orderValue = OrderValue::from($order);

        Http::fake([
            \Illuminate\Support\Facades\App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getAuthorizationTransactionNullAuthorizationExpiresAtSuccessResponse()),
        ]);

        $transactionValue = $this->service->createFromWebhook($webhook, $orderValue);

        $this->assertNotEmpty($transactionValue->id);
        $this->assertDatabaseHas('orders_transactions', [
            'id' => $transactionValue->id,
            'source_id' => $webhook->adminGraphqlApiId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $sourceOrderId,
            'store_id' => $this->currentStore->id,
            'kind' => TransactionKind::AUTHORIZATION->value,
            'authorization_expires_at' => '2024-05-16 12:01:01',
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => null,
            'shop_currency' => 'CAD',
            'shop_amount' => 69838,
            'customer_currency' => 'USD',
            'customer_amount' => 51680,
            'transaction_source_name' => 'web',
        ]);

        Event::assertDispatched(
            TransactionCreatedEvent::class,
            function (TransactionCreatedEvent $event) use ($transactionValue) {
                $this->assertEquals($transactionValue->id, $event->transaction->id);

                return true;
            }
        );
    }

    public function testItCreatesAuthorizationTransactionFromWebhookNullAuthorizationExpiresAtTimestamps(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 9, 12, 1, 1));

        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $webhook = WebhookOrderTransactionsCreate::from(
            $this->loadFixtureData('order-transactions-create-authorization-webhook.json', 'Orders')
        );
        $webhook->processedAt = CarbonImmutable::now()->subDays(2);
        $sourceOrderId = sprintf('gid://shopify/Order/%d', $webhook->orderId);

        $order = Order::withoutEvents(function () use ($sourceOrderId) {
            return Order::factory()->create([
                'source_id' => $sourceOrderId,
                'store_id' => $this->currentStore->id,
                'shop_currency' => 'CAD',
                'customer_currency' => 'USD',
            ]);
        });
        $orderValue = OrderValue::from($order);

        Http::fake([
            \Illuminate\Support\Facades\App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getAuthorizationTransactionNullAuthorizationExpiresAtSuccessResponse()),
        ]);

        $transactionValue = $this->service->createFromWebhook($webhook, $orderValue);

        $this->assertNotEmpty($transactionValue->id);
        $this->assertDatabaseHas('orders_transactions', [
            'id' => $transactionValue->id,
            'source_id' => $webhook->adminGraphqlApiId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $sourceOrderId,
            'store_id' => $this->currentStore->id,
            'kind' => TransactionKind::AUTHORIZATION->value,
            'authorization_expires_at' => '2024-05-14 12:01:01',
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => null,
            'shop_currency' => 'CAD',
            'shop_amount' => 69838,
            'customer_currency' => 'USD',
            'customer_amount' => 51680,
            'transaction_source_name' => 'web',
        ]);

        Event::assertDispatched(
            TransactionCreatedEvent::class,
            function (TransactionCreatedEvent $event) use ($transactionValue) {
                $this->assertEquals($transactionValue->id, $event->transaction->id);

                return true;
            }
        );
    }

    public function testItCreatesSaleTransactionFromWebhook(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $webhook = WebhookOrderTransactionsCreate::from(
            $this->loadFixtureData('order-transactions-create-sale-webhook.json', 'Orders')
        );
        $sourceOrderId = sprintf('gid://shopify/Order/%d', $webhook->orderId);

        $order = Order::withoutEvents(function () use ($sourceOrderId) {
            return Order::factory()->create([
                'source_id' => $sourceOrderId,
                'store_id' => $this->currentStore->id,
                'shop_currency' => 'CAD',
                'customer_currency' => 'USD',
            ]);
        });
        $orderValue = OrderValue::from($order);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getSaleTransactionSuccessResponse()),
        ]);

        $transactionValue = $this->service->createFromWebhook($webhook, $orderValue);

        $this->assertNotEmpty($transactionValue->id);
        $this->assertDatabaseHas('orders_transactions', [
            'id' => $transactionValue->id,
            'source_id' => $webhook->adminGraphqlApiId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $sourceOrderId,
            'store_id' => $this->currentStore->id,
            'kind' => TransactionKind::SALE->value,
            'authorization_expires_at' => null,
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => null,
            'shop_currency' => 'CAD',
            'shop_amount' => 69838,
            'customer_currency' => 'USD',
            'customer_amount' => 51680,
            'transaction_source_name' => 'web',
        ]);

        Event::assertDispatched(
            TransactionCreatedEvent::class,
            function (TransactionCreatedEvent $event) use ($transactionValue) {
                $this->assertEquals($transactionValue->id, $event->transaction->id);

                return true;
            }
        );
    }

    public function testItCreatesCaptureTransactionFromWebhook(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $webhook = WebhookOrderTransactionsCreate::from(
            $this->loadFixtureData('order-transactions-create-capture-webhook.json', 'Orders')
        );
        $sourceOrderId = sprintf('gid://shopify/Order/%d', $webhook->orderId);

        $order = Order::withoutEvents(function () use ($sourceOrderId) {
            return Order::factory()->create([
                'source_id' => $sourceOrderId,
                'store_id' => $this->currentStore->id,
                'shop_currency' => 'CAD',
                'customer_currency' => 'USD',
            ]);
        });
        $orderValue = OrderValue::from($order);

        $parentTransaction = Transaction::withoutEvents(function () use ($order, $sourceOrderId) {
            return Transaction::factory()->create([
                'order_id' => $order->id,
                'order_name' => $order->name,
                'source_order_id' => $sourceOrderId,
                'source_id' => 'gid://shopify/OrderTransaction/5488058826881',
                'store_id' => $this->currentStore->id,
                'kind' => TransactionKind::AUTHORIZATION->value,
                'shop_currency' => 'CAD',
                'shop_amount' => 69838,
                'customer_currency' => 'USD',
                'customer_amount' => 51680,
            ]);
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getCaptureTransactionSuccessResponse()),
        ]);

        $transactionValue = $this->service->createFromWebhook($webhook, $orderValue);

        $this->assertNotEmpty($transactionValue->id);
        $this->assertDatabaseHas('orders_transactions', [
            'id' => $transactionValue->id,
            'source_id' => $webhook->adminGraphqlApiId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $sourceOrderId,
            'store_id' => $this->currentStore->id,
            'kind' => TransactionKind::CAPTURE->value,
            'authorization_expires_at' => null,
            'parent_transaction_id' => $parentTransaction->id,
            'parent_transaction_source_id' => $parentTransaction->source_id,
            'shop_currency' => 'CAD',
            'shop_amount' => 69838,
            'customer_currency' => 'USD',
            'customer_amount' => 51680,
            'transaction_source_name' => 'web',
        ]);

        Event::assertDispatched(
            TransactionCreatedEvent::class,
            function (TransactionCreatedEvent $event) use ($transactionValue) {
                $this->assertEquals($transactionValue->id, $event->transaction->id);

                return true;
            }
        );
    }

    public function testItCreatesTransactionFromWebhookOnNotFoundTransaction(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $webhook = WebhookOrderTransactionsCreate::from(
            $this->loadFixtureData('order-transactions-create-capture-webhook.json', 'Orders')
        );
        $sourceOrderId = sprintf('gid://shopify/Order/%d', $webhook->orderId);

        $order = Order::withoutEvents(function () use ($sourceOrderId) {
            return Order::factory()->create([
                'source_id' => $sourceOrderId,
                'store_id' => $this->currentStore->id,
                'shop_currency' => 'CAD',
                'customer_currency' => 'USD',
            ]);
        });
        $orderValue = OrderValue::from($order);

        $parentTransaction = Transaction::withoutEvents(function () use ($order, $sourceOrderId) {
            return Transaction::factory()->create([
                'order_id' => $order->id,
                'order_name' => $order->name,
                'source_order_id' => $sourceOrderId,
                'source_id' => 'gid://shopify/OrderTransaction/5488058826881',
                'store_id' => $this->currentStore->id,
                'kind' => TransactionKind::AUTHORIZATION->value,
                'shop_currency' => 'CAD',
                'shop_amount' => 69838,
                'customer_currency' => 'USD',
                'customer_amount' => 51680,
            ]);
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getTransactionNotFoundSuccessResponse()),
        ]);

        $transactionValue = $this->service->createFromWebhook($webhook, $orderValue);

        $this->assertNotEmpty($transactionValue->id);
        $this->assertDatabaseHas('orders_transactions', [
            'id' => $transactionValue->id,
            'source_id' => $webhook->adminGraphqlApiId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $sourceOrderId,
            'store_id' => $this->currentStore->id,
            'kind' => TransactionKind::CAPTURE->value,
            'authorization_expires_at' => null,
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => null,
            'shop_currency' => 'CAD',
            'shop_amount' => 0,
            'customer_currency' => 'USD',
            'customer_amount' => 51680,
            'transaction_source_name' => 'web',
        ]);

        Event::assertDispatched(
            TransactionCreatedEvent::class,
            function (TransactionCreatedEvent $event) use ($transactionValue) {
                $this->assertEquals($transactionValue->id, $event->transaction->id);

                return true;
            }
        );
    }

    public function testItGetsTransactionBySourceId(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $transaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->for($order)->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $actualTransaction = $this->service->getBySourceId($transaction->source_id);

        $this->assertEquals($transaction->id, $actualTransaction->id);
    }

    public function testFirstOrCreatesWithExistingTransaction(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $transaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->for($order)->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $this->assertDatabaseCount('orders_transactions', 1);

        $existingTransaction = $this->service->firstOrCreate(TransactionValue::from($transaction));

        $this->assertEquals($transaction->id, $existingTransaction->id);
        $this->assertDatabaseCount('orders_transactions', 1);

        Event::assertNotDispatched(TransactionCreatedEvent::class);
    }

    public function testFirstOrCreatesWithNewTransaction(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $transactionValue = TransactionValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $order->source_id,
        ]);

        $this->assertDatabaseCount('orders_transactions', 0);

        $actualTransaction = $this->service->firstOrCreate($transactionValue);

        $this->assertDatabaseCount('orders_transactions', 1);

        $this->assertDatabaseHas('orders_transactions', [
            'id' => $actualTransaction->id,
            'source_id' => $actualTransaction->sourceId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $actualTransaction->sourceOrderId,
            'store_id' => $this->currentStore->id,
            'kind' => $actualTransaction->kind->value,
            'gateway' => $actualTransaction->gateway,
            'payment_id' => $actualTransaction->paymentId,
            'status' => $actualTransaction->status->value,
            'transaction_source_name' => $actualTransaction->transactionSourceName,
            'shop_currency' => $actualTransaction->shopCurrency,
            'customer_currency' => $actualTransaction->customerCurrency,
            'shop_amount' => $actualTransaction->shopAmount->getMinorAmount()->toInt(),
            'customer_amount' => $actualTransaction->customerAmount->getMinorAmount()->toInt(),
            'unsettled_shop_amount' => $actualTransaction->unsettledShopAmount->getMinorAmount()->toInt(),
            'unsettled_customer_amount' => $actualTransaction->unsettledCustomerAmount->getMinorAmount()->toInt(),
        ]);

        Event::assertDispatched(
            TransactionCreatedEvent::class,
            function (TransactionCreatedEvent $event) use ($actualTransaction) {
                $this->assertEquals($actualTransaction->id, $event->transaction->id);

                return true;
            }
        );
    }

    #[DataProvider('shopifyErrorExceptionsProvider')]
    public function testItDoesNotFetchAndSaveTransactionsOnShopifyErrors(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        Http::fake([
            \Illuminate\Support\Facades\App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id]);
        });
        $orderValue = OrderValue::from($order);

        $this->service->fetchAndSaveTransactionsForOrder($orderValue);

        Event::assertNotDispatched(TransactionCreatedEvent::class);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotFetchAndSaveTransactionOnShopifyOrderIdError(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetTransactionsByOrderIdErrorResponse()),
        ]);
        $this->expectException(ShopifyQueryServerException::class);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $transactions = $this->service->fetchAndSaveTransactionsForOrder($orderValue);

        $this->assertCount(0, $transactions);
        $this->assertDatabaseCount('orders_transactions', 0);

        Event::assertNotDispatched(TransactionCreatedEvent::class);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotFetchAndSaveTransactionOnShopifyOrderNotFound(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetTransactionsByOrderIdOrderNotFoundResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $transactions = $this->service->fetchAndSaveTransactionsForOrder($orderValue);

        $this->assertCount(0, $transactions);
        $this->assertDatabaseCount('orders_transactions', 0);

        Event::assertNotDispatched(TransactionCreatedEvent::class);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItFetchesAndButDoesNotSaveExistingTransactions(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetTransactionsByOrderIdSuccessResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->count(2)
                ->state(new Sequence(
                    ['source_id' => 'gid://shopify/OrderTransaction/5472548814977'],
                    ['source_id' => 'gid://shopify/OrderTransaction/5472550453377'],
                ))->create([
                    'store_id' => $this->currentStore->id,
                    'order_id' => $order->id,
                    'created_at' => '2023-01-01 00:00:00',
                    'updated_at' => '2023-01-01 00:00:00', // verify that updated timestamp didn't change
                ]);
        });
        $this->assertDatabaseCount('orders_transactions', 2);

        $transactions = $this->service->fetchAndSaveTransactionsForOrder($orderValue);

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

        Event::assertNotDispatched(TransactionCreatedEvent::class);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItFetchesAndSavesTransactions(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetTransactionsByOrderIdSuccessResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $transactions = $this->service->fetchAndSaveTransactionsForOrder($orderValue);

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

        Event::assertDispatchedTimes(TransactionCreatedEvent::class, 2);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItFetchesAndSavesCaptureTransaction(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyGetCaptureTransactionsByOrderIdSuccessResponse()),
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $orderValue = OrderValue::from($order);

        $transactions = $this->service->fetchAndSaveTransactionsForOrder($orderValue);

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

        Event::assertDispatchedTimes(TransactionCreatedEvent::class, 2);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetLastestTransaction(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
            ['id' => '1', 'created_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString()],
            ['id' => '2', 'created_at' => CarbonImmutable::now()->subDay()->toDateTimeString()],
            ['id' => '3', 'created_at' => CarbonImmutable::now()->toDateTimeString()],
            ['id' => '4', 'created_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString()],
            ['id' => '5', 'created_at' => CarbonImmutable::now()->addDay()->toDateTimeString()],
        ))->create();

        $transaction = $this->service->getLatestTransaction('test-order-id');

        $this->assertEquals('4', $transaction->id);
    }

    public function testItGetsLatestTransactionWithKind(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
            ['id' => '1', 'created_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
            ['id' => '2', 'created_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
            ['id' => '3', 'created_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
            ['id' => '4', 'created_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
            ['id' => '5', 'created_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
        ))->create();

        $transaction = $this->service->getLatestTransaction('test-order-id', TransactionKind::CAPTURE);
        $this->assertEquals('4', $transaction->id);

        $transaction = $this->service->getLatestTransaction('test-order-id', TransactionKind::REFUND);
        $this->assertEquals('5', $transaction->id);

        $transaction = $this->service->getLatestTransaction('test-order-id', TransactionKind::SALE);
        $this->assertEquals('1', $transaction->id);

        $transaction = $this->service->getLatestTransaction('test-order-id', [TransactionKind::SALE, TransactionKind::CAPTURE]);
        $this->assertEquals('4', $transaction->id);
    }

    public function testItGetsByOrderId(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
            ['id' => '1', 'created_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
            ['id' => '2', 'created_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
            ['id' => '3', 'created_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
            ['id' => '4', 'created_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
            ['id' => '5', 'created_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
        ))->create();

        $transactions = $this->service->getByOrderId('test-order-id');
        $this->assertCount(5, $transactions);
    }

    public function testItGetsByOrderIdWithKind(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
            ['id' => '1', 'created_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
            ['id' => '2', 'created_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
            ['id' => '3', 'created_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
            ['id' => '4', 'created_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
            ['id' => '5', 'created_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
        ))->create();

        $transactions = $this->service->getByOrderId('test-order-id', TransactionKind::SALE);
        $this->assertCount(1, $transactions);

        $transactions = $this->service->getByOrderId('test-order-id', TransactionKind::AUTHORIZATION);
        $this->assertCount(1, $transactions);

        $transactions = $this->service->getByOrderId('test-order-id', TransactionKind::REFUND);
        $this->assertCount(2, $transactions);

        $transactions = $this->service->getByOrderId('test-order-id', [TransactionKind::SALE, TransactionKind::CAPTURE]);
        $this->assertCount(2, $transactions);
    }

    public function testItGetsByDatetimeRangeAndKinds(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $transactions = Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'processed_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'processed_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'processed_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '6', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE, 'status' => TransactionStatus::FAILURE],
            ))->create();
        });

        $expectedTransactions = [
            $transactions[2],
            $transactions[4],
            $transactions[3],
        ];

        $actualTransactions = $this->service->getTransactionsProcessedAtDatetimeRangeAndKinds(
            CarbonImmutable::now()->subSecond(),
            CarbonImmutable::now()->addDays(2)->addSecond(),
            [TransactionKind::SALE, TransactionKind::CAPTURE, TransactionKind::REFUND]
        );

        $this->assertEquals(count($expectedTransactions), count($actualTransactions));
        foreach ($expectedTransactions as $i => $expectedTransaction) {
            $this->assertEquals($expectedTransaction->id, $actualTransactions[$i]->id);
        }
    }

    public function testItGetsByDatetimeRangeAndEmptyKinds(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $transactions = Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'processed_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'processed_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'processed_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::CAPTURE, 'status' => TransactionStatus::FAILURE],
                ['id' => '5', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE],
            ))->create();
        });

        $expectedTransactions = [
            $transactions[0],
            $transactions[1],
            $transactions[2],
        ];

        $actualTransactions = $this->service->getTransactionsProcessedAtDatetimeRangeAndKinds(
            CarbonImmutable::now()->subDays(2),
            CarbonImmutable::now()->addSecond()
        );

        $this->assertEquals(count($expectedTransactions), count($actualTransactions));
        foreach ($expectedTransactions as $i => $expectedTransaction) {
            $this->assertEquals($expectedTransaction->id, $actualTransactions[$i]->id);
        }
    }

    public function testItGetsByDatetimeRangeAndSingleKind(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $transactions = Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'processed_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'processed_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'processed_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '6', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE, 'status' => TransactionStatus::FAILURE],
            ))->create();
        });

        $expectedTransaction = $transactions[4];

        $actualTransactions = $this->service->getTransactionsProcessedAtDatetimeRangeAndKinds(
            CarbonImmutable::now()->subSecond(),
            CarbonImmutable::now()->addDays(2)->addSecond(),
            TransactionKind::SALE,
        );

        $this->assertEquals(1, count($actualTransactions));
        $this->assertEquals($expectedTransaction->id, $actualTransactions[0]->id);
    }

    public function testItDoesNotGetTransactionsOutsideOfDatetimeRange(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'processed_at' => CarbonImmutable::now()->subDays(5)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'processed_at' => CarbonImmutable::now()->subDays(4)->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'processed_at' => CarbonImmutable::now()->subDays(3)->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'processed_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '6', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::SALE, 'status' => TransactionStatus::FAILURE],
            ))->create();
        });

        $actualTransactions = $this->service->getTransactionsProcessedAtDatetimeRangeAndKinds(
            CarbonImmutable::now()->subSecond(),
            CarbonImmutable::now()->addDays(2)->addSecond(),
            [TransactionKind::SALE, TransactionKind::CAPTURE, TransactionKind::REFUND]
        );

        $this->assertEmpty($actualTransactions);
    }
}
