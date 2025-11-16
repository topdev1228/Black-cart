<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Listeners;

use App;
use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Shopify\Listeners\SubscriptionDeactivatedUpdateMetafieldsListener;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Shopify\Values\Subscription as SubscriptionValue;
use App\Domain\Shopify\Values\SubscriptionDeactivatedEvent;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyCurrentAppInstallationResponsesTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyMetafieldsSetResponsesTestData;
use Tests\TestCase;

class SubscriptionDeactivatedUpdateMetafieldsListenerTest extends TestCase
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

    public function testItHandlesEvent(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push(static::getShopifyMetafieldsSetForSubscriptionStatusChangedSuccessResponse('cancelled')),
        ]);

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create([
                'store_id' => $this->store->id,
                'status' => SubscriptionStatus::CANCELLED,
            ]);
        });
        $event = SubscriptionDeactivatedEvent::from([
            'subscription' => SubscriptionValue::from($subscription),
        ]);

        $listener = resolve(SubscriptionDeactivatedUpdateMetafieldsListener::class);
        $listener->handle($event);

        Http::assertSentCount(2);
        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
