<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Services;

use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use Firebase\JWT\JWT;
use Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

class OAuthService
{
    public function getRedirect(): RedirectResponse
    {
        $scopes = [
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

        /** @psalm-suppress UndefinedInterfaceMethod */
        return Socialite::driver('shopify')
            ->stateless()
            ->scopes($scopes)
            ->redirect();
    }

    public function verify(): User
    {
        /** @psalm-suppress UndefinedInterfaceMethod */
        $oauth = Socialite::driver('shopify')->stateless()->user();

        App::context(jwtToken: JwtToken::from([
            'token' => JWT::encode(
                [],
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ]));

        Http::post('http://localhost:8080/api/stores', [
            'name' => $oauth->name,
            'domain' => $oauth->user['myshopify_domain'],
            'email' => $oauth->user['email'],
            'phone' => $oauth->user['phone'],
            'owner_name' => $oauth->user['shop_owner'],
            'currency' => $oauth->user['currency'],
            'primary_locale' => $oauth->user['primary_locale'],
            'address1' => $oauth->user['address1'],
            'address2' => isset($oauth->user['address2']) ? $oauth->user['address2'] : '',
            'city' => $oauth->user['city'],
            'state' => $oauth->user['province'],
            'state_code' => $oauth->user['province_code'],
            'country' => $oauth->user['country'],
            'country_code' => $oauth->user['country_code'],
            'country_name' => $oauth->user['country_name'],
            'iana_timezone' => $oauth->user['iana_timezone'],
            'ecommerce_platform' => 'shopify',
            'ecommerce_platform_store_id' => $oauth->id,
            'ecommerce_platform_plan' => $oauth->user['plan_name'],
            'ecommerce_platform_plan_name' => $oauth->user['plan_display_name'],
            'source' => $oauth->user['source'],
        ])->throwUnlessStatus(Response::HTTP_CREATED);

        App::context(jwtToken: JwtToken::from([
            'token' => JWT::encode(
                (new JwtPayload(
                    domain: $oauth->user['myshopify_domain'],
                ))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ]));

        $settings = [
            'shopify_oauth_token' => [
                'name' => 'shopify_oauth_token',
                'value' => $oauth->token,
                'is_secure' => true,
            ],
        ];

        if ($oauth->refreshToken !== null) {
            $settings['shopify_oauth_refresh_token'] = [
                'name' => 'shopify_oauth_refresh_token',
                'value' => $oauth->refreshToken,
                'is_secure' => true,
            ];
        }

        Http::patch('http://localhost:8080/api/stores/settings', [
            'settings' => $settings,
        ])->throwUnlessStatus(Response::HTTP_OK);

        return $oauth;
    }
}
