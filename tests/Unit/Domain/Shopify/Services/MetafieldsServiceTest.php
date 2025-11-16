<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Services;

use App;
use App\Domain\Shopify\Enums\StoreStatus;
use App\Domain\Shopify\Exceptions\InternalShopifyRequestException;
use App\Domain\Shopify\Services\MetafieldsService;
use App\Domain\Shopify\Services\ShopifyMetafieldsService;
use App\Domain\Shopify\Values\Collections\MetafieldCollection;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\Metafield as MetafieldValue;
use App\Domain\Shopify\Values\Program as ProgramValue;
use App\Domain\Shopify\Values\Subscription as SubscriptionValue;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyCurrentAppInstallationResponsesTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyMetafieldsSetResponsesTestData;
use Tests\TestCase;

class MetafieldsServiceTest extends TestCase
{
    use ShopifyMetafieldsSetResponsesTestData;
    use ShopifyCurrentAppInstallationResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $currentStore;
    protected MetafieldsService $metafieldsService;
    protected array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];

        $this->metafieldsService = resolve(MetafieldsService::class);
    }

    #[DataProvider('shopifyErrorExceptionsProvider')]
    public function testItDoesNotSetMetafieldsOnGetAppInstallationIdError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        // We catch all Shopify exceptions and throw InternalShopifyRequestException instead
        $this->expectException(InternalShopifyRequestException::class);

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->unlimitedMaxTbybItems()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        $this->metafieldsService->upsertProgramMetafields($programValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotSetMetafieldsOnSetMetafieldsError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push($responseJson, $httpStatusCode),
        ]);
        // We catch all Shopify exceptions and throw InternalShopifyRequestException instead
        $this->expectException(InternalShopifyRequestException::class);

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->unlimitedMaxTbybItems()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        $this->metafieldsService->upsertProgramMetafields($programValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotSetMetafieldsOnUserError(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push(static::getShopifyMetafieldsSetErrorResponse(), 200),
        ]);
        // We catch all Shopify exceptions and throw InternalShopifyRequestException instead
        $this->expectException(InternalShopifyRequestException::class);

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->unlimitedMaxTbybItems()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        $this->metafieldsService->upsertProgramMetafields($programValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItSetsMetafieldsForProgramSavedFixedDepositUnlimitedMaxTbybItem(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push(static::getShopifyMetafieldsSetForProgramSavedFixedDepositUnlimitedMaxTbybItemSuccessResponse()),
        ]);

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->fixedDeposit()->unlimitedMaxTbybItems()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);
        $actualMetafields = $this->metafieldsService->upsertProgramMetafields($programValue);

        $expectedMetafields = [
            [
                'id' => 'gid://shopify/Metafield/1',
                'namespace' => 'blackcart',
                'key' => 'program_name',
                'value' => $programValue->name,
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/2',
                'namespace' => 'blackcart',
                'key' => 'selling_plan_group_id',
                'value' => $programValue->shopifySellingPlanGroupId,
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/3',
                'namespace' => 'blackcart',
                'key' => 'selling_plan_id',
                'value' => $programValue->shopifySellingPlanId,
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/4',
                'namespace' => 'blackcart',
                'key' => 'try_period_days',
                'value' => (string) ($programValue->tryPeriodDays),
                'type' => 'number_integer',
            ],
            [
                'id' => 'gid://shopify/Metafield/5',
                'namespace' => 'blackcart',
                'key' => 'min_tbyb_items',
                'value' => (string) ($programValue->minTbybItems),
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/6',
                'namespace' => 'blackcart',
                'key' => 'max_tbyb_items',
                'value' => 'unlimited',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/7',
                'namespace' => 'blackcart',
                'key' => 'deposit_type',
                'value' => $programValue->depositType->value,
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/8',
                'namespace' => 'blackcart',
                'key' => 'deposit_fixed',
                'value' => json_encode(
                    [
                        'amount' => number_format($programValue->depositValue / 100, 2),
                        'currency_code' => $programValue->currency->value,
                    ]
                ),
                'type' => 'money',
            ],
            [
                'id' => 'gid://shopify/Metafield/9',
                'namespace' => 'blackcart',
                'key' => 'deposit_percentage',
                'value' => '0',
                'type' => 'number_integer',
            ],
        ];

        $this->validateMetafields($expectedMetafields, $actualMetafields);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItSetsMetafieldsForProgramSavedFixedDepositUnlimitedMaxTbybItemMocked(): void
    {
        // The only difference is the Shopify Metafield ID, which we don't care about
        $expectedMetafields = MetafieldValue::collection([
            MetafieldValue::builder()->string('program_name', 'Try Before You Buy')->create(),
            MetafieldValue::builder()->string('selling_plan_group_id', 'gid://shopify/SellingPlanGroup/12345')->create(),
            MetafieldValue::builder()->string('selling_plan_id', 'gid://shopify/SellingPlan/56789')->create(),
            MetafieldValue::builder()->integer('try_period_days', 7)->create(),
            MetafieldValue::builder()->string('min_tbyb_items', '1')->create(),
            MetafieldValue::builder()->string('max_tbyb_items', 'unlimited')->create(),
            MetafieldValue::builder()->string('deposit_type', 'fixed')->create(),
            MetafieldValue::builder()->money('deposit_fixed', 25, 'USD')->create(),
            MetafieldValue::builder()->integer('deposit_percentage', 0)->create(),
        ]);

        $this->mock(ShopifyMetafieldsService::class, function (MockInterface $mock) use ($expectedMetafields) {
            $mock->shouldReceive('getAppInstallationId')->andReturn('gid://shopify/AppInstallation/12345');
            $mock->shouldReceive('upsert')
                ->withArgs(function ($appInstallationId, $metafields) use ($expectedMetafields) {
                    $this->assertEquals('gid://shopify/AppInstallation/12345', $appInstallationId);
                    foreach ($metafields as $actualMetafield) {
                        $expectedMetafield = $expectedMetafields->first(function ($inputOutputMetafield) use ($actualMetafield) {
                            return $inputOutputMetafield->key === $actualMetafield->key;
                        });
                        $this->assertNotEmpty($expectedMetafield);
                        $this->assertEquals($expectedMetafield->value, $actualMetafield->value);
                        $this->assertEquals($expectedMetafield->type, $actualMetafield->type);
                    }

                    return true;
                })->andReturn($expectedMetafields);
        });

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->fixedDeposit()->unlimitedMaxTbybItems()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        $metafieldsService = resolve(MetafieldsService::class);
        $metafieldsService->upsertProgramMetafields($programValue);
    }

    public function testItSetsMetafieldsForProgramSavedPercentageDepositLimitedMaxTbybItem(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push(static::getShopifyMetafieldsSetForProgramSavedPercentageDepositLimitedMaxTbybItemSuccessResponse()),
        ]);

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);
        $actualMetafields = $this->metafieldsService->upsertProgramMetafields($programValue);

        $expectedMetafields = [
            [
                'id' => 'gid://shopify/Metafield/1',
                'namespace' => 'blackcart',
                'key' => 'program_name',
                'value' => $programValue->name,
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/2',
                'namespace' => 'blackcart',
                'key' => 'selling_plan_group_id',
                'value' => $programValue->shopifySellingPlanGroupId,
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/3',
                'namespace' => 'blackcart',
                'key' => 'selling_plan_id',
                'value' => $programValue->shopifySellingPlanId,
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/4',
                'namespace' => 'blackcart',
                'key' => 'try_period_days',
                'value' => (string) ($programValue->tryPeriodDays),
                'type' => 'number_integer',
            ],
            [
                'id' => 'gid://shopify/Metafield/5',
                'namespace' => 'blackcart',
                'key' => 'min_tbyb_items',
                'value' => (string) ($programValue->minTbybItems),
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/6',
                'namespace' => 'blackcart',
                'key' => 'max_tbyb_items',
                'value' => (string) ($programValue->maxTbybItems),
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/7',
                'namespace' => 'blackcart',
                'key' => 'deposit_type',
                'value' => $programValue->depositType->value,
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/8',
                'namespace' => 'blackcart',
                'key' => 'deposit_fixed',
                'value' => json_encode(
                    [
                        'amount' => '0',
                        'currency_code' => $programValue->currency->value,
                    ]
                ),
                'type' => 'money',
            ],
            [
                'id' => 'gid://shopify/Metafield/9',
                'namespace' => 'blackcart',
                'key' => 'deposit_percentage',
                'value' => (string) ($programValue->depositValue),
                'type' => 'number_integer',
            ],
        ];

        $this->validateMetafields($expectedMetafields, $actualMetafields);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItSetsMetafieldsForProgramSavedPercentageDepositLimitedMaxTbybItemMocked(): void
    {
        // The only difference is the Shopify Metafield ID, which we don't care about
        $exptectedMetafields = MetafieldValue::collection([
            MetafieldValue::builder()->string('program_name', 'Try Before You Buy')->create(),
            MetafieldValue::builder()->string('selling_plan_group_id', 'gid://shopify/SellingPlanGroup/12345')->create(),
            MetafieldValue::builder()->string('selling_plan_id', 'gid://shopify/SellingPlan/56789')->create(),
            MetafieldValue::builder()->integer('try_period_days', 7)->create(),
            MetafieldValue::builder()->string('min_tbyb_items', '1')->create(),
            MetafieldValue::builder()->string('max_tbyb_items', '4')->create(),
            MetafieldValue::builder()->string('deposit_type', 'percentage')->create(),
            MetafieldValue::builder()->money('deposit_fixed', 0, 'USD')->create(),
            MetafieldValue::builder()->integer('deposit_percentage', 10)->create(),
        ]);

        $this->mock(ShopifyMetafieldsService::class, function (MockInterface $mock) use ($exptectedMetafields) {
            $mock->shouldReceive('getAppInstallationId')->andReturn('gid://shopify/AppInstallation/12345');
            $mock->shouldReceive('upsert')
                ->withArgs(function ($appInstallationId, $metafields) use ($exptectedMetafields) {
                    $this->assertEquals('gid://shopify/AppInstallation/12345', $appInstallationId);
                    foreach ($metafields as $actualMetafield) {
                        $expectedMetafield = $exptectedMetafields->first(function ($inputOutputMetafield) use ($actualMetafield) {
                            return $inputOutputMetafield->key === $actualMetafield->key;
                        });
                        $this->assertNotEmpty($expectedMetafield);
                        $this->assertEquals($expectedMetafield->value, $actualMetafield->value);
                        $this->assertEquals($expectedMetafield->type, $actualMetafield->type);
                    }

                    return true;
                })->andReturn($exptectedMetafields);
        });

        $programValue = ProgramValue::builder()->withShopifySellingPlanIds()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        $metafieldsService = resolve(MetafieldsService::class);
        $metafieldsService->upsertProgramMetafields($programValue);
    }

    public function testItSetsMetafieldsForStoreStatusChangedEvent(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push(static::getShopifyMetafieldsSetForStoreStatusChangedSuccessResponse()),
        ]);

        $actualMetafields = $this->metafieldsService->upsertStoreStatusMetefields(StoreStatus::ACTIVE);

        $expectedMetafields = [
            [
                'id' => 'gid://shopify/Metafield/10',
                'namespace' => 'blackcart',
                'key' => 'store_status',
                'value' => 'active',
                'type' => 'single_line_text_field',
            ],
        ];

        $this->validateMetafields($expectedMetafields, $actualMetafields);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItSetsMetafieldsForSubscriptionStatusChangedEvent(): void
    {
        $status = 'active';

        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse())
                ->push(static::getShopifyMetafieldsSetForSubscriptionStatusChangedSuccessResponse($status)),
        ]);

        $subscriptionValue = SubscriptionValue::builder()->withShopifyData()->create([
            'store_id' => $this->currentStore->id,
            'status' => $status,
            'activated_at' => Date::now(),
        ]);

        $actualMetafields = $this->metafieldsService->upsertSubscriptionStatusMetafield($subscriptionValue);

        $expectedMetafields = [
            [
                'id' => 'gid://shopify/Metafield/11',
                'namespace' => 'blackcart',
                'key' => 'subscription_status',
                'value' => $status,
                'type' => 'single_line_text_field',
            ],
        ];

        $this->validateMetafields($expectedMetafields, $actualMetafields);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotSetMetafieldsForSubscriptionStatusChangedEventOnShopify402Error(): void
    {
        $status = 'cancelled';

        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push('{"errors":"Unavailable Shop"}', 402),
        ]);

        $subscriptionValue = SubscriptionValue::builder()->withShopifyData()->create([
            'store_id' => $this->currentStore->id,
            'status' => $status,
            'activated_at' => now()->subDay(),
        ]);

        $actualMetafields = $this->metafieldsService->upsertSubscriptionStatusMetafield($subscriptionValue);
        $this->assertEmpty($actualMetafields);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    private function validateMetafields(array $expectedMetafields, MetafieldCollection $actualMetafields): void
    {
        $this->assertCount(count($expectedMetafields), $actualMetafields);

        for ($i = 0; $i < count($expectedMetafields); $i++) {
            $this->assertEquals($expectedMetafields[$i]['id'], $actualMetafields[$i]->id);
            $this->assertEquals($expectedMetafields[$i]['key'], $actualMetafields[$i]->key);
            $this->assertEquals($expectedMetafields[$i]['value'], $actualMetafields[$i]->value);
            $this->assertEquals($expectedMetafields[$i]['type'], $actualMetafields[$i]->type);
        }
    }
}
