<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Services;

use App;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shopify\Services\ShopifyMetafieldsService;
use App\Domain\Shopify\Values\Collections\MetafieldCollection;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\Metafield as MetafieldValue;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyCurrentAppInstallationResponsesTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyMetafieldsSetResponsesTestData;
use Tests\TestCase;

class ShopifyMetafieldsServiceTest extends TestCase
{
    use ShopifyMetafieldsSetResponsesTestData;
    use ShopifyCurrentAppInstallationResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $currentStore;
    protected ShopifyMetafieldsService $shopifyMetafieldsService;
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

        $this->shopifyMetafieldsService = resolve(ShopifyMetafieldsService::class);
    }

    #[DataProvider('shopifyErrorExceptionsProvider')]
    public function testItDoesNotGetAppInstallationIdOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        $this->shopifyMetafieldsService->getAppInstallationId();

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetsAppInstallationId(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyCurrentAppInstallationQuerySuccessResponse()),
        ]);

        $expectedAppInstallationId = 'gid://shopify/AppInstallation/432744464523';
        $actualAppInstallationId = $this->shopifyMetafieldsService->getAppInstallationId();

        $this->assertEquals($expectedAppInstallationId, $actualAppInstallationId);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotSetMetafieldsOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        $this->shopifyMetafieldsService->upsert(
            'gid://shopify/AppInstallation/432744464523',
            MetafieldValue::collection([]),
        );

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
                ->push(static::getShopifyMetafieldsSetErrorResponse(), 200),
        ]);
        $this->expectException(ShopifyMutationClientException::class);

        $this->shopifyMetafieldsService->upsert(
            'gid://shopify/AppInstallation/432744464523',
            MetafieldValue::collection([]),
        );

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
                ->push(static::getShopifyMetafieldsSetForProgramSavedFixedDepositUnlimitedMaxTbybItemSuccessResponse()),
        ]);

        $expectedMetafields = [
            [
                'id' => 'gid://shopify/Metafield/1',
                'key' => 'program_name',
                'value' => 'Try Before You Buy',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/2',
                'key' => 'selling_plan_group_id',
                'value' => 'gid://shopify/SellingPlanGroup/12345',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/3',
                'key' => 'selling_plan_id',
                'value' => 'gid://shopify/SellingPlan/56789',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/4',
                'key' => 'try_period_days',
                'value' => '7',
                'type' => 'number_integer',
            ],
            [
                'id' => 'gid://shopify/Metafield/5',
                'key' => 'min_tbyb_items',
                'value' => '1',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/6',
                'key' => 'max_tbyb_items',
                'value' => 'unlimited',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/7',
                'key' => 'deposit_type',
                'value' => 'fixed',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/8',
                'key' => 'deposit_fixed',
                'value' => '{"amount":"25.00","currency_code":"USD"}',
                'type' => 'money',
            ],
            [
                'id' => 'gid://shopify/Metafield/9',
                'key' => 'deposit_percentage',
                'value' => '0',
                'type' => 'number_integer',
            ],
        ];

        $actualMetafields = $this->shopifyMetafieldsService->upsert(
            'gid://shopify/AppInstallation/432744464523',
            MetafieldValue::collection(
                [
                    MetafieldValue::builder()->string('program_name', 'Try Before You Buy')->create(),
                    MetafieldValue::builder()->string(
                        'selling_plan_group_id',
                        'gid://shopify/SellingPlanGroup/12345'
                    )->create(),
                    MetafieldValue::builder()->string(
                        'selling_plan_id',
                        'gid://shopify/SellingPlan/56789'
                    )->create(),
                    MetafieldValue::builder()->integer('try_period_days', 7)->create(),
                    MetafieldValue::builder()->string('min_tbyb_items', '1')->create(),
                    MetafieldValue::builder()->string('max_tbyb_items', 'unlimited')->create(),
                    MetafieldValue::builder()->string('deposit_type', 'fixed')->create(),
                    MetafieldValue::builder()->money('deposit_fixed', 25, 'USD')->create(),
                    MetafieldValue::builder()->integer('deposit_percentage', 0)->create(),
                ]
            ),
        );

        $this->validateMetafields($expectedMetafields, $actualMetafields);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItSetsMetafieldsForProgramSavedPercentageDepositLimitedMaxTbybItem(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyMetafieldsSetForProgramSavedPercentageDepositLimitedMaxTbybItemSuccessResponse()),
        ]);

        $expectedMetafields = [
            [
                'id' => 'gid://shopify/Metafield/1',
                'key' => 'program_name',
                'value' => 'Try Before You Buy',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/2',
                'key' => 'selling_plan_group_id',
                'value' => 'gid://shopify/SellingPlanGroup/12345',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/3',
                'key' => 'selling_plan_id',
                'value' => 'gid://shopify/SellingPlan/56789',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/4',
                'key' => 'try_period_days',
                'value' => '7',
                'type' => 'number_integer',
            ],
            [
                'id' => 'gid://shopify/Metafield/5',
                'key' => 'min_tbyb_items',
                'value' => '1',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/6',
                'key' => 'max_tbyb_items',
                'value' => '4',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/7',
                'key' => 'deposit_type',
                'value' => 'percentage',
                'type' => 'single_line_text_field',
            ],
            [
                'id' => 'gid://shopify/Metafield/8',
                'key' => 'deposit_fixed',
                'value' => '{"amount":"0","currency_code":"USD"}',
                'type' => 'money',
            ],
            [
                'id' => 'gid://shopify/Metafield/9',
                'key' => 'deposit_percentage',
                'value' => '10',
                'type' => 'number_integer',
            ],
        ];

        $actualMetafields = $this->shopifyMetafieldsService->upsert(
            'gid://shopify/AppInstallation/432744464523',
            MetafieldValue::collection(
                [
                    MetafieldValue::builder()->string('program_name', 'Try Before You Buy')->create(),
                    MetafieldValue::builder()->string(
                        'selling_plan_group_id',
                        'gid://shopify/SellingPlanGroup/12345'
                    )->create(),
                    MetafieldValue::builder()->string(
                        'selling_plan_id',
                        'gid://shopify/SellingPlan/56789'
                    )->create(),
                    MetafieldValue::builder()->integer('try_period_days', 7)->create(),
                    MetafieldValue::builder()->string('min_tbyb_items', '1')->create(),
                    MetafieldValue::builder()->string('max_tbyb_items', '4')->create(),
                    MetafieldValue::builder()->string('deposit_type', 'fixed')->create(),
                    MetafieldValue::builder()->money('deposit_fixed', 0, 'USD')->create(),
                    MetafieldValue::builder()->integer('deposit_percentage', 10)->create(),
                ]
            ),
        );

        $this->validateMetafields($expectedMetafields, $actualMetafields);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItSetsMetafieldsForStoreStatusChanged(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyMetafieldsSetForStoreStatusChangedSuccessResponse()),
        ]);

        $expectedMetafields = [
            [
                'id' => 'gid://shopify/Metafield/10',
                'key' => 'store_status',
                'value' => 'active',
                'type' => 'single_line_text_field',
            ],
        ];

        $actualMetafields = $this->shopifyMetafieldsService->upsert(
            'gid://shopify/AppInstallation/432744464523',
            MetafieldValue::collection(
                [
                    MetafieldValue::builder()->string('store_status', 'active')->create(),
                ]
            ),
        );

        $this->validateMetafields($expectedMetafields, $actualMetafields);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItSetsMetafieldsForSubscriptionStatusChanged(): void
    {
        $status = 'active';

        Http::fake([
            App::context()->store->domain . '/admin/api/*' => Http::sequence()
                ->push(static::getShopifyMetafieldsSetForSubscriptionStatusChangedSuccessResponse($status)),
        ]);

        $expectedMetafields = [
            [
                'id' => 'gid://shopify/Metafield/11',
                'key' => 'subscription_status',
                'value' => $status,
                'type' => 'single_line_text_field',
            ],
        ];

        $actualMetafields = $this->shopifyMetafieldsService->upsert(
            'gid://shopify/AppInstallation/432744464523',
            MetafieldValue::collection(
                [
                    MetafieldValue::builder()->string('subscription_status', $status)->create(),
                ]
            ),
        );

        $this->validateMetafields($expectedMetafields, $actualMetafields);

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
