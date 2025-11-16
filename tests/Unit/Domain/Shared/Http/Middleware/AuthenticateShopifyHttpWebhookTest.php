<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Http\Middleware;

use App;
use App\Domain\Shared\Http\Middleware\AuthenticateShopifyHttpWebhook;
use App\Domain\Shopify\Values\WebhookCustomersDataRequest as WebhookCustomersDataRequestValue;
use App\Domain\Shopify\Values\WebhookCustomersRedact as WebhookCustomersRedactValue;
use App\Domain\Shopify\Values\WebhookShopRedact as WebhookShopRedactValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Repositories\StoreRepository;
use App\Exceptions\AuthenticationException;
use Config;
use Illuminate\Http\Response;
use Request;
use Tests\TestCase;

class AuthenticateShopifyHttpWebhookTest extends TestCase
{
    protected Store $store;
    protected string $shopifyClientSecret;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        $this->shopifyClientSecret = config('services.shopify.client_secret');
    }

    public function testItDoesNotAuthenticateOnMissingSignature(): void
    {
        $request = Request::create(
            '/webhooks/customers_data_request',
            'POST',
            [],
            [],
            [],
            [],
            json_encode(WebhookCustomersDataRequestValue::builder()->create([
                'shop_domain' => $this->store->domain,
            ])->toArray()),
        );
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('X-Shopify-Topic', 'customers/data_request');
        $request->headers->set('X-Shopify-Shop-Domain', $this->store->domain);
        $request->headers->set('X-Shopify-API-Verson', '2024-01');
        $request->headers->set('X-Shopify-Webhook-Id', 'random_id_123');
        $request->headers->set('X-Shopify-Triggered-At', '2024-01-07T18:00:27.877041743Z');
        // Did not set the X-Shopify-Hmac-Sha256 header

        $this->expectException(AuthenticationException::class);

        $authenticateShopifyHttpWebhook = new AuthenticateShopifyHttpWebhook(resolve(StoreRepository::class));
        $authenticateShopifyHttpWebhook->handle($request, function ($request) {
            return new Response();
        });
    }

    public function testItDoesNotAuthenticateOnWrongSignature(): void
    {
        $body = (string) json_encode(WebhookCustomersDataRequestValue::builder()->create([
            'shop_domain' => $this->store->domain,
        ])->toArray());

        $request = Request::create(
            '/webhooks/customers_data_request',
            'POST',
            [],
            [],
            [],
            [],
            $body,
        );
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('X-Shopify-Topic', 'customers/data_request');
        $request->headers->set('X-Shopify-Shop-Domain', $this->store->domain);
        $request->headers->set('X-Shopify-API-Verson', '2024-01');
        $request->headers->set('X-Shopify-Webhook-Id', 'random_id_123');
        $request->headers->set('X-Shopify-Triggered-At', '2024-01-07T18:00:27.877041743Z');
        $request->headers->set('X-Shopify-Hmac-Sha256', base64_encode(
            hash_hmac(
                'sha256',
                $body,
                'bogus_shopify_client_secret',
                true,
            ),
        ));

        $this->expectException(AuthenticationException::class);

        $authenticateShopifyHttpWebhook = new AuthenticateShopifyHttpWebhook(resolve(StoreRepository::class));
        $authenticateShopifyHttpWebhook->handle($request, function ($request) {
            return new Response();
        });
    }

    public function testItAuthenticates(): void
    {
        $testCases = [
            'customers/data_request' => [
                'controller_method_name' => 'customers_data_request',
                'topic' => 'customers/data_request',
                'body' => (string) json_encode(WebhookCustomersDataRequestValue::builder()->create([
                    'shop_domain' => $this->store->domain,
                ])->toArray()),
            ],
            'customers/redact' => [
                'controller_method_name' => 'customers_redact',
                'topic' => 'customers/redact',
                'body' => (string) json_encode(WebhookCustomersRedactValue::builder()->create([
                    'shop_domain' => $this->store->domain,
                ])->toArray()),
            ],
            'shop/redact' => [
                'controller_method_name' => 'shop_redact',
                'topic' => 'shop/redact',
                'body' => (string) json_encode(WebhookShopRedactValue::builder()->create([
                    'shop_domain' => $this->store->domain,
                ])->toArray()),
            ],
        ];

        foreach ($testCases as $testCase) {
            $request = Request::create(
                sprintf('/webhooks/%s', $testCase['controller_method_name']),
                'POST',
                [],
                [],
                [],
                [],
                $testCase['body'],
            );
            $request->headers->set('Content-Type', 'application/json');
            $request->headers->set('X-Shopify-Topic', $testCase['topic']);
            $request->headers->set('X-Shopify-Hmac-Sha256', base64_encode(
                hash_hmac(
                    'sha256',
                    $testCase['body'],
                    (string) $this->shopifyClientSecret,
                    true,
                ),
            ));
            $request->headers->set('X-Shopify-Shop-Domain', $this->store->domain);
            $request->headers->set('X-Shopify-API-Verson', '2024-01');
            $request->headers->set('X-Shopify-Webhook-Id', 'random_id_123');
            $request->headers->set('X-Shopify-Triggered-At', '2024-01-07T18:00:27.877041743Z');

            $authenticateShopifyHttpWebhook = new AuthenticateShopifyHttpWebhook(resolve(StoreRepository::class));
            $response = $authenticateShopifyHttpWebhook->handle($request, function ($request) {
                return new Response();
            });

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertEquals($this->store->id, App::context()->store->id);
            $this->assertEquals($this->store->domain, App::context()->store->domain);
            $this->assertNotEmpty(App::context()->jwtToken->token);
        }
    }
}
