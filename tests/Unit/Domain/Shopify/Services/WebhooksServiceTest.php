<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Services;

use App;
use App\Domain\Shopify\Enums\WebhookTopic;
use App\Domain\Shopify\Services\ShopifyWebhookService;
use App\Domain\Shopify\Services\WebhooksService;
use App\Domain\Shopify\Values\MandatoryWebhook as MandatoryWebhookValue;
use App\Domain\Shopify\Values\WebhookCustomersDataRequest as WebhookCustomersDataRequestValue;
use App\Domain\Shopify\Values\WebhookCustomersRedact as WebhookCustomersRedactValue;
use App\Domain\Shopify\Values\WebhookShopRedact as WebhookShopRedactValue;
use App\Domain\Stores\Models\Store;
use Tests\TestCase;

class WebhooksServiceTest extends TestCase
{
    protected Store $currentStore;
    protected WebhooksService $webhooksService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        $this->webhooksService = resolve(WebhooksService::class);
    }

    public function testItCreatesCustomersDataRequest(): void
    {
        $webhookCustomersDataRequestValue = WebhookCustomersDataRequestValue::builder()->create();

        $actual = $this->webhooksService->createCustomersDataRequest(
            $this->currentStore->id,
            $webhookCustomersDataRequestValue,
        );

        $expected = MandatoryWebhookValue::builder()->customersDataRequest()->create([
            'id' => $actual->id,
            'store_id' => $this->currentStore->id,
        ]);

        $this->validate($expected, $actual);
    }

    public function testItCreatesCustomersRedact(): void
    {
        $webhookCustomersRedact = WebhookCustomersRedactValue::builder()->create();

        $actual = $this->webhooksService->createCustomersRedact(
            $this->currentStore->id,
            $webhookCustomersRedact,
        );

        $expected = MandatoryWebhookValue::builder()->customersRedact()->create([
            'id' => $actual->id,
            'store_id' => $this->currentStore->id,
        ]);

        $this->validate($expected, $actual);
    }

    public function testItCreatesShopRedact(): void
    {
        $webhookShopRedact = WebhookShopRedactValue::builder()->create();

        $actual = $this->webhooksService->createShopRedact(
            $this->currentStore->id,
            $webhookShopRedact,
        );

        $expected = MandatoryWebhookValue::builder()->create([
            'id' => $actual->id,
            'store_id' => $this->currentStore->id,
        ]);

        $this->validate($expected, $actual);
    }

    private function validate(MandatoryWebhookValue $expected, MandatoryWebhookValue $actual): void
    {
        $this->assertNotEmpty($actual->id);
        $this->assertEquals($expected->storeId, $actual->storeId);
        $this->assertEquals($expected->topic, $actual->topic);
        $this->assertEquals($expected->shopifyShopId, $actual->shopifyShopId);
        $this->assertEquals($expected->shopifyDomain, $actual->shopifyDomain);
        $this->assertEquals($expected->status, $actual->status);
        $this->assertEquals($expected->data, $actual->data);

        $dbExpected = $expected->toArray();
        $dbExpected['data'] = json_encode($dbExpected['data']);

        $this->assertDatabaseHas('shopify_mandatory_webhooks', $dbExpected);
    }

    public function testItSubscribesAllWebhooks(): void
    {
        $shopifyWebhookService = $this->mock(ShopifyWebhookService::class);
        $shopifyWebhookService->shouldReceive('subscribe')
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

        $webhookService = resolve(WebhooksService::class);
        $webhookService->subscribe();
    }
}
