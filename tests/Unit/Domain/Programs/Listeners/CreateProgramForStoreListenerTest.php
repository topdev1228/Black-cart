<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Programs\Listeners;

use App;
use App\Domain\Programs\Events\ProgramSavedEvent;
use App\Domain\Programs\Listeners\CreateProgramForStoreListener;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Events\StoreCreated;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Config;
use Event;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Programs\Traits\ProgramConfigurationsTestData;
use Tests\Fixtures\Domains\Programs\Traits\ShopifySellingPlanGroupResponsesTestData;
use Tests\TestCase;

class CreateProgramForStoreListenerTest extends TestCase
{
    use ProgramConfigurationsTestData;
    use ShopifySellingPlanGroupResponsesTestData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.shopify.client_secret', 'super-secret-key');
    }

    private function setupContextForCurrency(string $currency): void
    {
        $this->store = Store::withoutEvents(function () use ($currency) {
            return Store::factory()->create(['currency' => $currency]);
        });

        App::context(store: $this->store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $this->store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));
    }

    #[DataProvider('currencyProvider')]
    public function testItCreatesProgram(string $currency): void
    {
        $this->setupContextForCurrency($currency);

        $this->assertDatabaseCount('programs', 0);

        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupCreateSuccessResponse()),
        ]);

        Event::fake([
            ProgramSavedEvent::class,
        ]);

        new StoreCreated(StoreValue::from($this->store));
        $eventHandler = resolve(CreateProgramForStoreListener::class);
        $eventHandler->handle();

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        $this->assertDatabaseCount('programs', 1);

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->create([
            'store_id' => $this->store->id,
            'currency' => $this->store->currency,
            'max_tbyb_items' => null,
        ]);
        $expectedProgramValue = $programValue->toArray();
        // assertDatabaseHas fails if id is null in the value object but it's not null in the database
        unset($expectedProgramValue['id']);
        $this->assertDatabaseHas('programs', $expectedProgramValue);

        Event::assertDispatched(ProgramSavedEvent::class);
    }

    public static function currencyProvider(): array
    {
        return [
            ['USD'],
            ['CAD'],
            ['JPY'],
            ['GBP'],
            ['AUD'],
        ];
    }
}
