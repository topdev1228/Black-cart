<?php
declare(strict_types=1);

namespace Feature\Domain\Shopify\Http\Controllers;

use App\Domain\Shopify\Values\WebhookCustomersDataRequest as WebhookCustomersDataRequestValue;
use App\Domain\Shopify\Values\WebhookCustomersRedact as WebhookCustomersRedactValue;
use App\Domain\Shopify\Values\WebhookShopRedact as WebhookShopRedactValue;
use App\Domain\Stores\Models\Store;
use Config;
use Tests\TestCase;

class WebhooksControllerTest extends TestCase
{
    protected Store $store;
    protected array $headers;
    protected string $shopifyClientSecret;

    const WEBHOOKS_PATH = '/webhooks/';

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        $this->shopifyClientSecret = config('services.shopify.client_secret');

        $this->headers = [
            'Content-Type' => 'application/json',
            'X-Shopify-Shop-Domain' => $this->store->domain,
            'X-Shopify-API-Verson' => '2024-01',
            'X-Shopify-Webhook-Id' => 'random_id_123',
            'X-Shopify-Triggered-At' => '2024-01-07T18:00:27.877041743Z',
        ];
    }

    public function testItDoesNotHandleWebhookOnMissingSignatureHeader(): void
    {
        $webhookCustomersDataRequestValue = WebhookCustomersDataRequestValue::builder()->create([
            'shop_domain' => $this->store->domain,
        ]);

        $this->headers['X-Shopify-Topic'] = 'customers/data_request';
        // Did not set the X-Shopify-Hmac-Sha256 header

        $this->assertDatabaseEmpty('shopify_mandatory_webhooks');

        $response = $this->postJson(
            static::WEBHOOKS_PATH . 'customers_data_request',
            $webhookCustomersDataRequestValue->toArray(),
            $this->headers
        );
        $response->assertStatus(401);
        $response->assertJson([
            'type' =>'request_error',
            'code' =>'invalid_login',
            'message' =>'Missing Shopify signature header.',
            'errors' =>[],
        ]);

        $this->assertDatabaseEmpty('shopify_mandatory_webhooks');
    }

    public function testItDoesNotHandleWebhookOnInvalidSignatureHeader(): void
    {
        $webhookCustomersDataRequestValue = WebhookCustomersDataRequestValue::builder()->create([
            'shop_domain' => $this->store->domain,
        ]);

        $this->headers['X-Shopify-Topic'] = 'customers/data_request';
        $this->headers['X-Shopify-Hmac-Sha256'] = base64_encode(
            hash_hmac(
                'sha256',
                (string) json_encode($webhookCustomersDataRequestValue->toArray()),
                'bogus_shopify_client_secret',
                true,
            ),
        );

        $this->assertDatabaseEmpty('shopify_mandatory_webhooks');

        $response = $this->postJson(
            static::WEBHOOKS_PATH . 'customers_data_request',
            $webhookCustomersDataRequestValue->toArray(),
            $this->headers
        );
        $response->assertStatus(401);
        $response->assertJson([
            'type' =>'request_error',
            'code' =>'invalid_login',
            'message' =>'Invalid Shopify signature header.',
            'errors' =>[],
        ]);

        $this->assertDatabaseEmpty('shopify_mandatory_webhooks');
    }

    public function testItHandlesCustomersDataRequest(): void
    {
        $webhookCustomersDataRequestValue = WebhookCustomersDataRequestValue::builder()->create([
            'shop_domain' => $this->store->domain,
        ]);

        $this->headers['X-Shopify-Topic'] = 'customers/data_request';
        $this->headers['X-Shopify-Hmac-Sha256'] = base64_encode(
            hash_hmac(
                'sha256',
                (string) json_encode($webhookCustomersDataRequestValue->toArray()),
                (string) $this->shopifyClientSecret,
                true
            )
        );

        $this->assertDatabaseEmpty('shopify_mandatory_webhooks');

        $response = $this->postJson(
            static::WEBHOOKS_PATH . 'customers_data_request',
            $webhookCustomersDataRequestValue->toArray(),
            $this->headers
        );
        $response->assertStatus(200);
        $response->assertContent('');

        $this->assertDatabaseCount('shopify_mandatory_webhooks', 1);
    }

    public function testItHandlesCustomersRedact(): void
    {
        $webhookCustomersRedactValue = WebhookCustomersRedactValue::builder()->create([
            'shop_domain' => $this->store->domain,
        ]);

        $this->headers['X-Shopify-Topic'] = 'customers/redact';
        $this->headers['X-Shopify-Hmac-Sha256'] = base64_encode(
            hash_hmac(
                'sha256',
                (string) json_encode($webhookCustomersRedactValue->toArray()),
                (string) $this->shopifyClientSecret,
                true
            )
        );

        $this->assertDatabaseEmpty('shopify_mandatory_webhooks');

        $response = $this->postJson(
            static::WEBHOOKS_PATH . 'customers_redact',
            $webhookCustomersRedactValue->toArray(),
            $this->headers
        );
        $response->assertStatus(200);
        $response->assertContent('');

        $this->assertDatabaseCount('shopify_mandatory_webhooks', 1);
    }

    public function testItHandlesShopRedact(): void
    {
        $webhookShopRedactValue = WebhookShopRedactValue::builder()->create([
            'shop_domain' => $this->store->domain,
        ]);

        $this->headers['X-Shopify-Topic'] = 'shop/redact';
        $this->headers['X-Shopify-Hmac-Sha256'] = base64_encode(
            hash_hmac(
                'sha256',
                (string) json_encode($webhookShopRedactValue->toArray()),
                (string) $this->shopifyClientSecret,
                true
            )
        );

        $this->assertDatabaseEmpty('shopify_mandatory_webhooks');

        $response = $this->postJson(
            static::WEBHOOKS_PATH . 'shop_redact',
            $webhookShopRedactValue->toArray(),
            $this->headers
        );
        $response->assertStatus(200);
        $response->assertContent('');

        $this->assertDatabaseCount('shopify_mandatory_webhooks', 1);
    }
}
