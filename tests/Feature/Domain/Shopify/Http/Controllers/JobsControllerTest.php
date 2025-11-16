<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Shopify\Http\Controllers;

use App;
use App\Domain\Shopify\Values\Job as JobValue;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Enums\Exceptions\ApiExceptionTypes;
use Config;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\Fixtures\Domains\Shopify\Traits\ShopifyBulkOperationResponsesTestData;
use Tests\TestCase;

class JobsControllerTest extends TestCase
{
    use ShopifyBulkOperationResponsesTestData;
    use ShopifyErrorsTestData;

    private Store $currentStore;
    private array $headers;

    const JOBS_API_URL = '/api/shopify/jobs';

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }

    public function testItReturnsUnauthorizedErrorWhenNoStoreContextSentOnPost(): void
    {
        App::context(store: StoreValue::from(StoreValue::empty()));
        $this->headers = [];

        $jobValue = JobValue::builder()->create(['store_id' => $this->currentStore->id]);
        $this->postJson(static::JOBS_API_URL, $jobValue->toArray(), $this->headers)
            ->assertStatus(401);
    }

    public function testItDoesNotCreateJobOnMissingRequiredParameters(): void
    {
        $requiredParameters = [
            'store_id' => 'store id',
            'query' => 'query',
            'domain' => 'domain',
            'topic' => 'topic',
        ];

        $data = [
            'store_id' => 'store_id_123_abc',
            'query' => 'query',
            'domain' => 'products',
            'topic' => 'topic',
        ];

        foreach ($requiredParameters as $requiredParameter => $requiredParameterName) {
            $dataEmpty = $data;
            $dataEmpty[$requiredParameter] = '';
            $dataUnset = $data;
            unset($dataUnset[$requiredParameter]);

            foreach ([$dataEmpty, $dataUnset] as $inputData) {
                $response = $this->postJson(static::JOBS_API_URL, $inputData, $this->headers);

                $response->assertStatus(422);
                $response->assertJsonFragment([
                    'type' => ApiExceptionTypes::REQUEST_ERROR,
                    'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
                    'message' => $requiredParameterName . ' is required.',
                    'errors' => [
                        $requiredParameter => [
                            $requiredParameterName . ' is required.',
                        ],
                    ],
                ]);
            }
        }
    }

    public function testItDoesNotCreateJobOnInvalidDomainValue(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'query' => 'query',
            'domain' => 'invalid_domain',
            'topic' => 'topic',
        ];

        $response = $this->postJson(static::JOBS_API_URL, $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'The selected domain is invalid.',
            'errors' => [
                'domain' => [
                    'The selected domain is invalid.',
                ],
            ],
        ]);
    }

    #[DataProvider('shopifyErrorApiResponsesProvider')]
    public function testItDoesNotCreateJobOnShopifyError(
        array $responseJson,
        int $httpStatusCode,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);

        $jobValue = JobValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $response = $this->postJson(static::JOBS_API_URL, $jobValue->toArray(), $this->headers);
        $response->assertStatus(500);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::API_ERROR->value,
            'code' => ApiExceptionErrorCodes::SERVER_ERROR->value,
            'message' => 'Internal call to Shopify failed, please try again in a few minutes.',
            'errors' => [],
        ]);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public static function shopifyErrorApiResponsesProvider(): array
    {
        return [
            'On HTTP request error' => [
                ['errors' => '400 error'],
                400,
            ],
            'On HTTP server error' => [
                ['errors' => '500 error'],
                500,
            ],
            'On Shopify authentication error' => [
                static::getShopifyAdminApiAuthenticationErrorResponse(),
                401,
            ],
            'On Shopify mutation server error' => [
                static::getShopifyAdminApiErrorResponse(),
                500,
            ],
            'On Shopify mutation user error' => [
                static::getShopifyBulkOperationCreateErrorResponse(),
                400,
            ],
        ];
    }

    public function testItCreatesJob(): void
    {
        $jobValue = JobValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyBulkOperationCreateSuccessResponse()),
        ]);

        $response = $this->postJson(static::JOBS_API_URL, $jobValue->toArray(), $this->headers);

        $response->assertStatus(201);

        $actualResponse = $response->decodeResponseJson();

        $expectedJobValue = JobValue::builder()->create([
            'id' => $actualResponse['job']['id'],
            'store_id' => $this->currentStore->id,
            'query' => $jobValue->query,
            'domain' => $jobValue->domain,
            'topic' => $jobValue->topic,
            'shopify_job_id' => $actualResponse['job']['shopify_job_id'],
            'status' => $actualResponse['job']['status'],
        ])->toArray();

        $response->assertJsonStructure([
            'job' => [
                'id',
                'store_id',
                'query',
                'domain',
                'topic',
                'shopify_job_id',
                'status',
                'export_file_url',
                'export_partial_file_url',
                'error_code',
            ],
        ]);
        $response->assertJsonFragment($expectedJobValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        $this->assertDatabaseHas('shopify_jobs', $expectedJobValue);
    }
}
