<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Listeners;

use App;
use App\Domain\Shopify\Enums\StoreStatus;
use App\Domain\Shopify\Enums\WebhookTopic;
use App\Domain\Shopify\Listeners\StoreStatusChangedUpdateMetafieldsListener;
use App\Domain\Shopify\Services\MetafieldsService;
use App\Domain\Shopify\Services\ShopifyWebhookService;
use App\Domain\Shopify\Services\WebhooksService;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Shopify\Values\StoreStatusChangedEvent;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyCurrentAppInstallationResponsesTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyMetafieldsSetResponsesTestData;
use Tests\TestCase;

class StoreStatusChangedUpdateMetafieldsListenerTest extends TestCase
{
    use ShopifyCurrentAppInstallationResponsesTestData;
    use ShopifyMetafieldsSetResponsesTestData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $this->store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $this->store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));
    }

    public function testItHandlesEventForStoreStatusActive(): void
    {
        $event = StoreStatusChangedEvent::from(['status' => StoreStatus::ACTIVE->value]);

        $metafieldService = $this->mock(MetafieldsService::class);
        $metafieldService
            ->shouldReceive('upsertStoreStatusMetefields')
            ->once()
            ->with(StoreStatus::ACTIVE);

        $shopifyWebhookService = $this->mock(ShopifyWebhookService::class);
        $shopifyWebhookService
            ->shouldReceive('subscribe')
            ->once()
            ->with(
                WebhookTopic::APP_SUBSCRIPTIONS_UPDATE,
                WebhookTopic::APP_UNINSTALLED,
                WebhookTopic::BULK_OPERATIONS_FINISH,
                WebhookTopic::FULFILLMENT_EVENTS_CREATE,
                WebhookTopic::ORDER_TRANSACTIONS_CREATE,
                WebhookTopic::ORDERS_CANCELLED,
                WebhookTopic::ORDERS_CREATE,
                WebhookTopic::PAYMENT_SCHEDULES_DUE,
                WebhookTopic::REFUNDS_CREATE,
                WebhookTopic::RETURNS_APPROVE,
            );

        $metafieldService = resolve(MetafieldsService::class);
        $webhookService = resolve(WebhooksService::class);

        $listener = new StoreStatusChangedUpdateMetafieldsListener($metafieldService, $webhookService);
        $listener->handle($event);
    }

    public function testItHandlesEventForStoreStatusNotActive(): void
    {
        $event = new StoreStatusChangedEvent(
            StoreStatus::PAUSED
        );

        $metafieldService = $this->mock(MetafieldsService::class);
        $metafieldService
            ->shouldReceive('upsertStoreStatusMetefields')
            ->once()
            ->with(StoreStatus::PAUSED);

        $webhookService = $this->mock(WebhooksService::class);
        $webhookService->shouldNotReceive('subscribe');

        $listener = new StoreStatusChangedUpdateMetafieldsListener($metafieldService, $webhookService);
        $listener->handle($event);
    }
}
