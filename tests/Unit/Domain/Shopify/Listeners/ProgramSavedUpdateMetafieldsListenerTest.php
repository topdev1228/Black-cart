<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Listeners;

use App;
use App\Domain\Programs\Models\Program;
use App\Domain\Shopify\Listeners\ProgramSavedUpdateMetafieldsListener;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Shopify\Values\ProgramSavedEvent;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyCurrentAppInstallationResponsesTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyMetafieldsSetResponsesTestData;
use Tests\TestCase;

class ProgramSavedUpdateMetafieldsListenerTest extends TestCase
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

    public function testItHandlesEventForFixedDepositUnlimitedMaxTbybItem(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push(static::getShopifyMetafieldsSetForProgramSavedFixedDepositUnlimitedMaxTbybItemSuccessResponse()),
        ]);

        $event = ProgramSavedEvent::from([
            'program' => Program::factory()->withShopifySellingPlanIds()->fixedDeposit()->unlimitedMaxTbybItems()
                ->create([
                    'store_id' => $this->store->id,
                    'currency' => $this->store->currency,
                ]),
        ]);

        $listener = resolve(ProgramSavedUpdateMetafieldsListener::class);
        $listener->handle($event);

        Http::assertSentCount(2);
        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItHandlesEventForPercentageDepositLimitedMaxTbybItem(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push(static::getShopifyMetafieldsSetForProgramSavedPercentageDepositLimitedMaxTbybItemSuccessResponse()),
        ]);

        $event = ProgramSavedEvent::from([
            'program' => Program::factory()->withShopifySellingPlanIds()
                ->create([
                    'store_id' => $this->store->id,
                    'currency' => $this->store->currency,
                ]),
        ]);

        $listener = resolve(ProgramSavedUpdateMetafieldsListener::class);
        $listener->handle($event);

        Http::assertSentCount(2);
        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
