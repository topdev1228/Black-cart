<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Services;

use App\Domain\Shopify\Services\OAuthService;
use Config;
use Http;
use Laravel\Socialite\Two\User;
use Mockery\MockInterface;
use function resolve;
use Socialite;
use Tests\TestCase;

class OAuthServiceTest extends TestCase
{
    public function testItGetsRedirect(): void
    {
        request()->query->add(['shop' => 'test.myshopify.com']);

        $clientId = urlencode(Config::get('services.shopify.client_id'));
        $redirectUri = urlencode(Config::get('services.shopify.redirect'));

        $expectedScopes = [
            'read_cart_transforms',
            'write_cart_transforms',
            'write_checkouts',
            'read_customer_payment_methods',
            'read_customers',
            'read_discounts',
            'read_fulfillments',
            'unauthenticated_read_metaobjects',
            'read_metaobject_definitions',
            'write_metaobject_definitions',
            'read_metaobjects',
            'write_metaobjects',
            'read_orders',
            'write_orders',
            'read_payment_mandate',
            'write_payment_mandate',
            'read_payment_terms',
            'write_payment_terms',
            'read_price_rules',
            'write_price_rules',
            'read_products',
            'write_products',
            'unauthenticated_read_product_listings',
            'read_product_listings',
            'unauthenticated_read_product_tags',
            'read_purchase_options',
            'write_purchase_options',
            'read_reports',
            'write_reports',
            'read_returns',
            'write_returns',
            'unauthenticated_read_selling_plans',
            'read_themes',
            'write_themes',
            'read_order_edits',
            'write_order_edits',
        ];

        $oauthService = resolve(OAuthService::class);
        $redirect = $oauthService->getRedirect();
        $this->assertEquals(
            'https://test.myshopify.com/admin/oauth/authorize?client_id=' . $clientId .
            '&redirect_uri=' . $redirectUri . '&scope=' . implode('%2C', $expectedScopes) .
            '&response_type=code',
            $redirect->getTargetUrl()
        );
    }

    public function testItVerifies(): void
    {
        Config::set('services.shopify.client_secret', 'test_client_secret');

        Socialite::expects('driver->stateless->user')->andReturn(
            $this->mock(User::class, function (MockInterface $mock) {
                $mock->id = 'shopify_shop_id_1';
                $mock->name = 'Test Store';
                $mock->token = 'test_token';
                $mock->refreshToken = 'test_refresh_token';
                $mock->user = [
                    'myshopify_domain' => 'test.myshopify.com',
                    'email' => 'davey@blackcart.co',
                    'shop_owner' => 'Davey Shafik',
                    'source' => 'google',
                    'phone' => '4165551234',
                    'currency' => 'USD',
                    'primary_locale' => 'en',
                    'address1' => '123 Test St',
                    'city' => 'Toronto',
                    'province' => 'Ontario',
                    'province_code' => 'ON',
                    'country' => 'Canada',
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'iana_timezone' => 'America/New_York',
                    'plan_display_name' => 'Developer Preview',
                    'plan_name' => 'partner_test',
                ];
            })
        );

        Http::fake([
            '*/api/stores' => Http::response([
                'store' => [
                    'id' => 1,
                    'name' => 'Test Store',
                    'domain' => 'test.myshopify.com',
                    'access_token' => 'test_token',
                    'email' => 'davey@blackcart.co',
                    'phone' => '4165551234',
                    'owner_name' => 'Davey Shafik',
                    'currency' => 'USD',
                    'primary_locale' => 'en',
                    'address1' => '123 Test St',
                    'address2' => null,
                    'city' => 'Toronto',
                    'state' => 'Ontario',
                    'state_code' => 'ON',
                    'country' => 'Canada',
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'iana_timezone' => 'America/New_York',
                    'ecommerce_platform' => 'shopify',
                    'ecommerce_platform_store_id' => 'shopify_shop_id_1',
                    'ecommerce_platform_plan' => 'partner_test',
                    'ecommerce_platform_plan_name' => 'Developer Preview',
                    'source' => 'google',
                ],
            ], 201),
            '*/api/stores/settings' => Http::response([
                'settings' => [
                    'test' => ['name' => 'test', 'value' => 'value'],
                    'test2' => ['name' => 'test2', 'value' => 'value2'],
                ],
            ], 200),
        ]);

        $oauthService = resolve(OAuthService::class);
        $oauth = $oauthService->verify();

        $this->assertEquals('test.myshopify.com', $oauth->user['myshopify_domain']);
        $this->assertEquals('test_token', $oauth->token);
    }
}
