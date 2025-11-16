<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Programs\Services;

use App;
use App\Domain\Programs\Enums\DepositType;
use App\Domain\Programs\Services\ShopifyProgramService;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Programs\Traits\ProgramConfigurationsTestData;
use Tests\Fixtures\Domains\Programs\Traits\ShopifySellingPlanGroupResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class ShopifyProgramServiceTest extends TestCase
{
    use ProgramConfigurationsTestData;
    use ShopifySellingPlanGroupResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $currentStore;
    protected ShopifyProgramService $shopifyProgramService;
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

        $this->shopifyProgramService = resolve(ShopifyProgramService::class);
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotCreateSellingPlanGroupOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $programValue = $this->caseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifyProgramService->create($programValue);
        $this->validate();
    }

    public function testItDoesNotCreateSellingPlanGroupOnShopifySellingPlanGroupCreateUserError(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupCreateUserErrorResponse(),
            200,
            ShopifyMutationClientException::class,
        );
        $this->shopifyProgramService->create($programValue);
        $this->validate();
    }

    #[DataProvider('programConfigurationsProvider')]
    public function testItCreatesSellingPlanGroup(
        int $tryPeriodDays,
        DepositType $depositType,
        int $depositValue,
    ): void {
        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'try_period_days' => $tryPeriodDays,
            'deposit_type' => $depositType,
            'deposit_value' => $depositValue,
            'currency' => $this->currentStore->currency,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupCreateSuccessResponse()),
        ]);

        $expectedProgramValue = ProgramValue::builder()->withShopifySellingPlanIds()->create([
            'store_id' => $this->currentStore->id,
            'try_period_days' => $tryPeriodDays,
            'deposit_type' => $depositType,
            'deposit_value' => $depositValue,
            'currency' => $this->currentStore->currency,
        ]);

        $programActual = $this->shopifyProgramService->create($programValue);

        $this->validate();

        $this->assertEquals($expectedProgramValue->storeId, $programActual->storeId);
        $this->assertEquals($expectedProgramValue->shopifySellingPlanGroupId, $programActual->shopifySellingPlanGroupId);
        $this->assertEquals($expectedProgramValue->shopifySellingPlanId, $programActual->shopifySellingPlanId);
        $this->assertEquals($expectedProgramValue->tryPeriodDays, $programActual->tryPeriodDays);
        $this->assertEquals($expectedProgramValue->depositType, $programActual->depositType);
        $this->assertEquals($expectedProgramValue->depositValue, $programActual->depositValue);
        $this->assertEquals($expectedProgramValue->minTbybItems, $programActual->minTbybItems);
        $this->assertEquals($expectedProgramValue->maxTbybItems, $programActual->maxTbybItems);
        $this->assertEquals($expectedProgramValue->dropOffDays, $programActual->dropOffDays);
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotUpdateSellingPlanGroupOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $programValue = $this->caseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifyProgramService->update($programValue);
        $this->validate();
    }

    public function testItDoesNotUpdateSellingPlanGroupOnShopifySellingPlanGroupUpdateUserError(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupUpdateUserErrorResponse(),
            200,
            ShopifyMutationClientException::class
        );
        $this->shopifyProgramService->update($programValue);
        $this->validate();
    }

    #[DataProvider('programConfigurationsProvider')]
    public function testItUpdatesSellingPlanGroup(
        int $tryPeriodDays,
        DepositType $depositType,
        int $depositValue,
    ): void {
        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'try_period_days' => $tryPeriodDays,
            'deposit_type' => $depositType,
            'deposit_value' => $depositValue,
            'currency' => $this->currentStore->currency,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupCreateSuccessResponse()),
        ]);

        $this->shopifyProgramService->update($programValue);

        $this->validate();
    }

    private function caseSetup(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException = '',
    ): ProgramValue {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);

        if ($expectedException !== '') {
            $this->expectException($expectedException);
        }

        return ProgramValue::builder()->withShopifySellingPlanIds()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);
    }

    private function validate(): void
    {
        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
