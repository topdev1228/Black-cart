<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Programs\Services;

use App;
use App\Domain\Programs\Services\ShopifyProgramProductService;
use App\Domain\Programs\Values\Program as ProgramValue;
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

class ShopifyProgramProductServiceTest extends TestCase
{
    use ProgramConfigurationsTestData;
    use ShopifySellingPlanGroupResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $currentStore;
    protected ShopifyProgramProductService $shopifyProgramProductService;
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

        $this->shopifyProgramProductService = resolve(ShopifyProgramProductService::class);
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotRemoveProductsFromTbybProgramOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $programValue = $this->caseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifyProgramProductService->removeProducts(
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
        $this->shopifyProgramProductService->removeProducts(
            $programValue->shopifySellingPlanGroupId,
            ['gid://shopify/products/4'],
        );
        $this->validate();
    }

    public function testItGetsProductsFromShopifySellingPlan(): void
    {
        $programValue = $this->caseSetup(
            static::getSellingPlanProductResponse(),
            200,
            '',
        );
        $this->shopifyProgramProductService->getProducts(
            $programValue->shopifySellingPlanGroupId,
        );
        $this->validate();
    }

    public function testItDoesNothingWhenInputIsEmpty(): void
    {
        $programValue = $this->caseSetup(
            static::getShopifySellingPlanGroupRemoveProductsSuccessResponse(),
            200,
            '',
        );
        $this->shopifyProgramProductService->removeProducts(
            $programValue->shopifySellingPlanGroupId,
            [],
        );
        Http::assertNothingSent();
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
