<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Http\Middleware;

use App\Domain\Shared\Http\Middleware\LogShopifyWebhook;
use App\Domain\Shared\Repositories\ShopifyWebhookDataRepository;
use App\Domain\Shopify\Enums\WebhookTopic;
use Illuminate\Http\Response;
use Request;
use Tests\TestCase;

class LogShopifyWebhookTest extends TestCase
{
    public function testItLogsShopifyWebhooksViaGooglePubsub(): void
    {
        $shopifyWebhookDataRepository = $this->mock(ShopifyWebhookDataRepository::class, function ($mock) {
            $mock->shouldReceive('save')
                ->once()
                ->withArgs(function ($topic, $data, $attributes) {
                    $this->assertEquals(WebhookTopic::PRODUCTS_CREATE, $topic);
                    $this->assertEquals(['test' => 'data'], $data);
                    $this->assertEquals(['X-Shopify-Shop-Domain' => 'test.myshopify.com'], $attributes);

                    return true;
                });
        });

        $logShopifyWebhook = new LogShopifyWebhook($shopifyWebhookDataRepository);

        $request = Request::create('/test', 'POST', [], [], [], [], json_encode([
            'message' => [
                'subscription' => 'shopify-webhook-products-create',
                'data' => ['test' => 'data'],
                'attributes' => ['X-Shopify-Shop-Domain' => 'test.myshopify.com'],
            ],
        ], JSON_THROW_ON_ERROR));

        $request->headers->set('Content-Type', 'application/json');

        $response = $logShopifyWebhook->handle($request, function ($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testItLogsShopifyWebhooksViaHttp(): void
    {
        $shopifyWebhookDataRepository = $this->mock(ShopifyWebhookDataRepository::class, function ($mock) {
            $mock->shouldReceive('save')
                ->once()
                ->withArgs(function ($topic, $data, $attributes) {
                    $this->assertEquals(WebhookTopic::PRODUCTS_CREATE, $topic);
                    $this->assertEquals(['test' => 'data'], $data);
                    $this->assertArrayHasKey('x-shopify-shop-domain', $attributes);
                    $this->assertContains('test.myshopify.com', $attributes['x-shopify-shop-domain']);
                    $this->assertArrayHasKey('x-shopify-topic', $attributes);
                    $this->assertContains('products/create', $attributes['x-shopify-topic']);

                    return true;
                });
        });

        $logShopifyWebhook = new LogShopifyWebhook($shopifyWebhookDataRepository);

        $request = Request::create('/test', 'POST', [], [], [], [], json_encode(['test' => 'data']));

        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('X-Shopify-Shop-Domain', 'test.myshopify.com');
        $request->headers->set('X-Shopify-Topic', 'products/create');

        $response = $logShopifyWebhook->handle($request, function ($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testItIgnoresNonShopifyWebhooks(): void
    {
        $shopifyWebhookDataRepository = $this->mock(ShopifyWebhookDataRepository::class);
        $shopifyWebhookDataRepository->shouldNotReceive('save');

        $logShopifyWebhook = new LogShopifyWebhook($shopifyWebhookDataRepository);

        $request = Request::create('/test', 'POST', [], [], [], [], json_encode([
            'message' => [
                'subscription' => 'non-shopify-webhook',
                'data' => 'test-data',
                'attributes' => ['test-attribute' => 'value'],
            ],
        ], JSON_THROW_ON_ERROR));

        $request->headers->set('Content-Type', 'application/json');

        $response = $logShopifyWebhook->handle($request, function ($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->getStatusCode());
    }
}
