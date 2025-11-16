<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shopify\Services;

use App;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shopify\Services\ShopifyJobsService;
use App\Domain\Shopify\Values\Job as JobValue;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyBulkOperationResponsesTestData;
use Tests\TestCase;

class ShopifyJobsServiceTest extends TestCase
{
    use ShopifyBulkOperationResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $currentStore;
    protected ShopifyJobsService $shopifyJobsService;
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

        $this->shopifyJobsService = resolve(ShopifyJobsService::class);
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotCreateShopifyBulkOperationOnError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $jobValue = $this->caseSetup($responseJson, $httpStatusCode, $expectedException);
        $this->shopifyJobsService->createQuery($jobValue);
        $this->validate();
    }

    public function testItDoesNotCreateShopifyBulkOperationOnUserError(): void
    {
        $jobValue = $this->caseSetup(
            static::getShopifyBulkOperationCreateErrorResponse(),
            200,
            ShopifyMutationClientException::class,
        );
        $this->shopifyJobsService->createQuery($jobValue);
        $this->validate();
    }

    public function testItCreatesShopifyBulkOperation(): void
    {
        $jobValue = JobValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyBulkOperationCreateSuccessResponse()),
        ]);

        $expectedJobValue = JobValue::builder()->shopifyBulkOperationCreated()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $actualJobValue = $this->shopifyJobsService->createQuery($jobValue);

        $this->validate();

        $this->assertEquals($expectedJobValue->storeId, $actualJobValue->storeId);
        $this->assertEquals($expectedJobValue->query, $actualJobValue->query);
        $this->assertEquals($expectedJobValue->domain, $actualJobValue->domain);
        $this->assertEquals($expectedJobValue->topic, $actualJobValue->topic);
        $this->assertEquals($expectedJobValue->shopifyJobId, $actualJobValue->shopifyJobId);
        $this->assertEquals($expectedJobValue->status, $actualJobValue->status);
    }

    #[DataProvider('shopifyErrorExceptionsProvider')]
    public function testItDoesNotGetJobDataOnShopifyError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        $jobValue = JobValue::builder()->shopifyBulkOperationCreated()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $this->shopifyJobsService->getJobData($jobValue);

        $this->validate();
    }

    public function testItGetsJobData(): void
    {
        $jobValue = JobValue::builder()->shopifyBulkOperationCreated()->create([
            'store_id' => $this->currentStore->id,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyBulkOperationFinishFileUrlSuccessResponse()),
        ]);

        $expectedJobValue = JobValue::builder()->shopifyBulkOperationCreated()->completed()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $actualJobValue = $this->shopifyJobsService->getJobData($jobValue);

        $this->validate();

        $this->assertEquals($expectedJobValue->exportFileUrl, $actualJobValue->exportFileUrl);
        $this->assertEquals($expectedJobValue->exportPartialFileUrl, $actualJobValue->exportPartialFileUrl);
    }

    private function caseSetup(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): JobValue {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        $this->expectException($expectedException);

        return JobValue::builder()->create([
            'store_id' => $this->currentStore->id,
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
