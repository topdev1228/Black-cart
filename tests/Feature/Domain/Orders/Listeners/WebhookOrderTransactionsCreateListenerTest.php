<?php
declare(strict_types=1);

namespace Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Events\TransactionCreatedEvent;
use App\Domain\Orders\Listeners\WebhookOrderTransactionsCreateListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Values\WebhookOrderTransactionsCreate;
use App\Domain\Stores\Models\Store;
use Event;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyGetTransactionResponsesTestData;
use Tests\TestCase;

class WebhookOrderTransactionsCreateListenerTest extends TestCase
{
    use ShopifyGetTransactionResponsesTestData;

    protected Store $currentStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);
    }

    public function testItDoesNotCreateTransactionNotFoundOrder(): void
    {
        $webhook = WebhookOrderTransactionsCreate::from(
            $this->loadFixtureData('order-transactions-create-capture-webhook.json', 'Orders')
        );

        $listener = resolve(WebhookOrderTransactionsCreateListener::class);
        $transactionValue = $listener->handle($webhook);

        $this->assertNull($transactionValue);
        $this->assertDatabaseEmpty('orders_transactions');
    }

    public function testItCreatesAuthorizationTransactionFromShopifyWebhook(): void
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

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getAuthorizationTransactionSuccessResponse()),
        ]);

        $listener = resolve(WebhookOrderTransactionsCreateListener::class);
        $transactionValue = $listener->handle($webhook);

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

    public function testItCreatesSaleTransactionFromShopifyWebhook(): void
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

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getSaleTransactionSuccessResponse()),
        ]);

        $listener = resolve(WebhookOrderTransactionsCreateListener::class);
        $transactionValue = $listener->handle($webhook);

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

    public function testItCreatesCaptureTransactionFromShopifyWebhook(): void
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
        $parentTransaction = Transaction::withoutEvents(function () use ($order, $sourceOrderId) {
            return Transaction::factory()->create([
                'order_id' => $order->id,
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

        $listener = resolve(WebhookOrderTransactionsCreateListener::class);
        $transactionValue = $listener->handle($webhook);

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

    public function testItCreatesTransactionFromShopifyWebhookOnNotFoundTransaction(): void
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
        $parentTransaction = Transaction::withoutEvents(function () use ($order, $sourceOrderId) {
            return Transaction::factory()->create([
                'order_id' => $order->id,
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

        $listener = resolve(WebhookOrderTransactionsCreateListener::class);
        $transactionValue = $listener->handle($webhook);

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
}
