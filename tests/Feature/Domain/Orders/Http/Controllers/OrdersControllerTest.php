<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Http\Controllers;

use App;
use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Models\Order;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Config;
use Event;
use Firebase\JWT\JWT;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyCurrentAppInstallationResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class OrdersControllerTest extends TestCase
{
    use ShopifyCurrentAppInstallationResponsesTestData;
    use ShopifyErrorsTestData;

    private Store $currentStore;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->currentStore));

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }

    public function testItDisplaysView(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $response = $this->get(route('orders.web.view.orders.end-early', [
            'storeId' => $this->currentStore->id,
            'id' => $order->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('End my Trial');
    }

    public function testItThrows404OnMissingOrder(): void
    {
        $response = $this->get(route('orders.web.view.orders.end-early', [
            'storeId' => $this->currentStore->id,
            'id' => 'non-existent-id',
        ]));

        $response->assertStatus(404);
    }

    public function testThatItDoesNotEndTrialBeforeExpiryOnOrderNotFound(): void
    {
        Event::fake();

        $response = $this->postJson(
            '/api/stores/orders/non-existent-order-id/end_trial_before_expiry',
            [],
            $this->headers
        );

        $response->assertStatus(404);
        Event::assertNotDispatched(PaymentRequiredEvent::class);
    }

    public function testThatItEndsTrialBeforeExpiry(): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'source_id' => '5095218610315',
                'store_id' => $this->currentStore->id,
            ]);
        });

        $response = $this->postJson(
            '/api/stores/orders/' . $order->id . '/end_trial_before_expiry',
            [],
            $this->headers
        );

        $response->assertStatus(202);
        Event::assertDispatched(PaymentRequiredEvent::class, function (PaymentRequiredEvent $event) use ($order) {
            $this->assertEquals($order->id, $event->orderId);
            $this->assertEquals($order->source_id, $event->sourceOrderId);
            $this->assertEquals($order->id, $event->trialGroupId);

            return true;
        });
    }

    public function testThatItDoesNotGetOrderNonExistentOrder(): void
    {
        $response = $this->getJson(
            '/api/stores/orders/non-existent-order-id',
            $this->headers
        );

        $response->assertStatus(404);
        $response->assertJson([
            'type' => 'request_error',
            'code' => 'resource_not_found',
            'message' => 'Order not found.',
            'errors' => [],
        ]);
    }

    public function testThatItDoesNotGetOrderWrongStore(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => '123456789',
            ]);
        });

        $response = $this->getJson(
            '/api/stores/orders/' . $order->id,
            $this->headers
        );

        $response->assertStatus(404);
        $response->assertJson([
            'type' => 'request_error',
            'code' => 'resource_not_found',
            'message' => 'Order not found.',
            'errors' => [],
        ]);
    }

    public function testThatItGetsOrder(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $response = $this->getJson(
            '/api/stores/orders/' . $order->id,
            $this->headers
        );

        dump($response->getContent());

        $response->assertStatus(200);
        $response->assertJson([
            'order' => [
                'id' => $order->id,
                'store_id' => $this->currentStore->id,
            ],
        ]);
    }
}
