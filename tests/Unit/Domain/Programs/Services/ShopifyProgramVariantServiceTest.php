<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Programs\Services;

use App;
use App\Domain\Programs\Services\ShopifyProgramVariantService;
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

class ShopifyProgramVariantServiceTest extends TestCase
{
    use ProgramConfigurationsTestData;
    use ShopifySellingPlanGroupResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $currentStore;
    protected ShopifyProgramVariantService $shopifyProgramVariantService;
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

        $this->shopifyProgramVariantService = resolve(ShopifyProgramVariantService::class);
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotAddProductsToTbybProgramOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $programValue = $this->caseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifyProgramVariantService->addProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/products/1', 'gid://shopify/products/2', 'gid://shopify/products/3'],
        );
        $this->validate();
    }

    public function testItDoesNotAddProductsToTbybProgramOnUserError(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupAddProductsErrorResponse(),
            200,
            ShopifyMutationClientException::class
        );
        $this->shopifyProgramVariantService->addProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/products/1', 'gid://shopify/products/2', 'gid://shopify/products/3'],
        );
        $this->validate();
    }

    public function testItDoesAddProductsToTbybProgram(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupAddProductsSuccessResponse(),
            200,
            '',
        );
        $this->shopifyProgramVariantService->addProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/products/1', 'gid://shopify/products/2', 'gid://shopify/products/3'],
        );
        $this->validate();
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotRemoveProductsFromTbybProgramOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $programValue = $this->caseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifyProgramVariantService->removeProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/products/4'],
        );
        $this->validate();
    }

    public function testItDoesNotRemoveProductsFromTbybProgramOnUserError(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupRemoveProductsErrorResponse(),
            200,
            ShopifyMutationClientException::class
        );
        $this->shopifyProgramVariantService->removeProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/products/4'],
        );
        $this->validate();
    }

    public function testItDoesRemoveProductsFromTbybProgram(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupRemoveProductsSuccessResponse(),
            200,
            '',
        );
        $this->shopifyProgramVariantService->removeProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/products/4'],
        );
        $this->validate();
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotAddProductVariantsToTbybProgramOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $programValue = $this->caseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifyProgramVariantService->addProductVariants(
            $programValue->shopifySellingPlanGroupId,
            [
                'gid://shopify/ProductVariant/1',
                'gid://shopify/ProductVariant/2',
                'gid://shopify/ProductVariant/3',
                'gid://shopify/ProductVariant/4',
            ],
        );
        $this->validate();
    }

    public function testItDoesNotAddProductVariantsToTbybProgramOnUserError(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupAddProductVariantsErrorResponse(),
            200,
            ShopifyMutationClientException::class
        );
        $this->shopifyProgramVariantService->addProductVariants(
            $programValue->shopifySellingPlanGroupId,
            [
                'gid://shopify/ProductVariant/1',
                'gid://shopify/ProductVariant/2',
                'gid://shopify/ProductVariant/3',
                'gid://shopify/ProductVariant/4',
            ],
        );
        $this->validate();
    }

    public function testItDoesAddProductVariantsToTbybProgram(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupAddProductVariantsSuccessResponse(),
            200,
            '',
        );
        $this->shopifyProgramVariantService->addProductVariants(
            $programValue->shopifySellingPlanGroupId,
            [
                'gid://shopify/ProductVariant/1',
                'gid://shopify/ProductVariant/2',
                'gid://shopify/ProductVariant/3',
                'gid://shopify/ProductVariant/4',
            ],
        );
        $this->validate();
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotRemoveProductVariantsFromTbybProgramOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $programValue = $this->caseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifyProgramVariantService->removeProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/ProductVariant/5', 'gid://shopify/ProductVariant/6'],
        );
        $this->validate();
    }

    public function testItDoesNotRemoveProductVariantsFromTbybProgramOnUserError(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupRemoveProductVariantsErrorResponse(),
            200,
            ShopifyMutationClientException::class
        );
        $this->shopifyProgramVariantService->removeProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/ProductVariant/5', 'gid://shopify/ProductVariant/6'],
        );
        $this->validate();
    }

    public function testItDoesRemoveProductVariantsFromTbybProgram(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupRemoveProductVariantsSuccessResponse(),
            200,
            '',
        );
        $this->shopifyProgramVariantService->removeProductVariants(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/ProductVariant/5', 'gid://shopify/ProductVariant/6'],
        );
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
