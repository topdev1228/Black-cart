<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Programs\Http\Controllers;

use App;
use App\Domain\Programs\Enums\DepositType;
use App\Domain\Programs\Events\ProgramSavedEvent;
use App\Domain\Programs\Models\Program;
use App\Domain\Programs\Repositories\ProgramRepository;
use App\Domain\Programs\Services\ShopifyProgramService;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Enums\Exceptions\ApiExceptionTypes;
use Config;
use Event;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Tests\Fixtures\Domains\Programs\Traits\ProgramConfigurationsTestData;
use Tests\Fixtures\Domains\Programs\Traits\ShopifySellingPlanGroupResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class ProgramControllerTest extends TestCase
{
    use ProgramConfigurationsTestData;
    use ShopifySellingPlanGroupResponsesTestData;
    use ShopifyErrorsTestData;

    private Store $currentStore;
    private array $headers;

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

    public function testItReturnsUnauthorizedErrorWhenNoStoreContextSentOnGet(): void
    {
        App::context(store: StoreValue::from(StoreValue::empty()));
        $this->headers = [];

        $this->getJson('/api/stores/programs', $this->headers)
            ->assertStatus(401);
    }

    public function testItReturnsUnauthorizedErrorWhenNoStoreContextSentOnPut(): void
    {
        App::context(store: StoreValue::from(StoreValue::empty()));
        $this->headers = [];

        $program = Program::factory()->create(['store_id' => $this->currentStore->id]);
        $program->name = 'New Name';

        $this->putJson('/api/stores/programs/' . $program->id, $program->toArray(), $this->headers)
            ->assertStatus(401);
    }

    public function testItReturnsUnauthorizedErrorWhenNoStoreContextSentOnPost(): void
    {
        App::context(store: StoreValue::from(StoreValue::empty()));
        $this->headers = [];

        $programValue = ProgramValue::builder()->create(['store_id' => $this->currentStore->id]);
        $this->postJson('/api/stores/programs', $programValue->toArray(), $this->headers)
            ->assertStatus(401);
    }

    public function testItGetsEmptyProgramsOnNonExistentPrograms(): void
    {
        $response = $this->getJson('/api/stores/programs', $this->headers);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'programs' => [],
        ]);
        $response->assertJsonCount(0, 'programs');
    }

    public function testItGetsPrograms(): void
    {
        $program = Program::factory()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
            'drop_off_days' => 2,
        ]);

        $response = $this->getJson('/api/stores/programs', $this->headers);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'programs' => [
                '*' => [
                    'id',
                    'store_id',
                    'name',
                    'shopify_selling_plan_group_id',
                    'shopify_selling_plan_id',
                    'try_period_days',
                    'deposit_type',
                    'deposit_value',
                    'currency',
                    'min_tbyb_items',
                    'max_tbyb_items',
                    'drop_off_days',
                ],
            ],
        ]);
        $response->assertJsonCount(1, 'programs');
        $response->assertJsonFragment([
            'programs' => [
                [
                    'id' => $program->id,
                    'name' => $program->name,
                    'store_id' => $this->currentStore->id,
                    'shopify_selling_plan_group_id' => $program->shopify_selling_plan_group_id,
                    'shopify_selling_plan_id' => $program->shopify_selling_plan_id,
                    'try_period_days' => $program->try_period_days,
                    'deposit_type' => $program->deposit_type,
                    'deposit_value' => $program->deposit_value,
                    'currency' => $program->currency,
                    'min_tbyb_items' => $program->min_tbyb_items,
                    'max_tbyb_items' => $program->max_tbyb_items,
                    'drop_off_days' => $program->drop_off_days,
                ],
            ],
        ]);
    }

    public function testItDoesNotCreateProgramOnMissingStoreId(): void
    {
        $data = [
            'name' => 'Try Before You Buy Test',
            'try_period_days' => 14,
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 300,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 1,
        ];

        $response = $this->postJson('/api/stores/programs', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'store id is required.',
            'errors' => [
                'store_id' => [
                    'store id is required.',
                ],
            ],
        ]);
    }

    public function testItDoesNotCreateProgramOnEmptyName(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'name' => '',
            'try_period_days' => 14,
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 300,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 1,
        ];

        $response = $this->postJson('/api/stores/programs', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'name is required.',
            'errors' => [
                'name' => [
                    'name is required.',
                ],
            ],
        ]);
    }

    public function testItDoesNotCreateProgramOnNegativeTryPeriodDays(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'name' => 'Try Before You Buy Test',
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 300,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 1,
        ];

        foreach ([0, -14] as $tryPeriodDays) {
            $data['try_period_days'] = $tryPeriodDays;

            $response = $this->postJson('/api/stores/programs', $data, $this->headers);

            $response->assertStatus(422);
            $response->assertJsonFragment([
                'type' => ApiExceptionTypes::REQUEST_ERROR,
                'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
                'message' => 'The try period days field must be at least 1.',
                'errors' => [
                    'try_period_days' => [
                        'The try period days field must be at least 1.',
                    ],
                ],
            ]);
        }
    }

    public function testItDoesNotCreateProgramOnInvalidDepositType(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'name' => 'Try Before You Buy Test',
            'try_period_days' => 14,
            'deposit_type' => 'invalid_deposit_type',
            'deposit_value' => 300,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 1,
        ];

        $response = $this->postJson('/api/stores/programs', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'The selected deposit type is invalid.',
            'errors' => [
                'deposit_type' => [
                    'The selected deposit type is invalid.',
                ],
            ],
        ]);
    }

    public function testItDoesNotCreateProgramOnNegativeDepositValue(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'name' => 'Try Before You Buy Test',
            'try_period_days' => 14,
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => -1,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 1,
        ];

        $response = $this->postJson('/api/stores/programs', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'The deposit value field must be at least 0.',
            'errors' => [
                'deposit_value' => [
                    'The deposit value field must be at least 0.',
                ],
            ],
        ]);
    }

    public function testItDoesNotCreateProgramOnNegativeMinItems(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'name' => 'Try Before You Buy Test',
            'try_period_days' => 14,
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 300,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => -1,
        ];

        $response = $this->postJson('/api/stores/programs', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'The min tbyb items field must be at least 1.',
            'errors' => [
                'min_tbyb_items' => [
                    'The min tbyb items field must be at least 1.',
                ],
            ],
        ]);
    }

    public function testItDoesNotCreateProgramOnNegativeMaxItems(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'name' => 'Try Before You Buy Test',
            'try_period_days' => 14,
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 300,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 1,
            'max_tbyb_items' => -1,
        ];

        $response = $this->postJson('/api/stores/programs', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'The max tbyb items field must be greater than or equal to 1.',
            'errors' => [
                'max_tbyb_items' => [
                    'The max tbyb items field must be greater than or equal to 1.',
                ],
            ],
        ]);
    }

    public function testItDoesNotCreateProgramOnMaxItemsLessThanMinItems(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'name' => 'Try Before You Buy Test',
            'try_period_days' => 14,
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 300,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 3,
            'max_tbyb_items' => 2,
        ];

        $response = $this->postJson('/api/stores/programs', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'The max tbyb items field must be greater than or equal to 3.',
            'errors' => [
                'max_tbyb_items' => [
                    'The max tbyb items field must be greater than or equal to 3.',
                ],
            ],
        ]);
    }

    public function testItDoesNotCreateProgramOnEmptyRequiredParameters(): void
    {
        $data = [
            'store_id' => 'store_id_123_abc',
            'name' => 'Try Before You Buy Test',
            'try_period_days' => 14,
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 300,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 3,
        ];

        foreach ($data as $key => $value) {
            $copyData = $data;
            $copyData[$key] = '';

            $response = $this->postJson('/api/stores/programs', $copyData, $this->headers);

            $response->assertStatus(422);
            $response->assertJsonStructure([
                'type',
                'code',
                'message',
                'errors' => [
                    $key,
                ],
            ]);

            $this->assertEquals(ApiExceptionTypes::REQUEST_ERROR->value, $response->json('type'));
            $this->assertEquals(ApiExceptionErrorCodes::INVALID_PARAMETERS->value, $response->json('code'));
        }
    }

    public function testItDoesNotCreateProgramOnHttpRequestError(): void
    {
        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(['errors' => '400 error'], 400),
        ]);

        $response = $this->postJson('/api/stores/programs', $programValue->toArray(), $this->headers);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_REQUEST,
            'errors' => [],
        ]);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotCreateProgramOnHttpServerError(): void
    {
        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(['errors' => '500 error'], 500),
        ]);

        $response = $this->postJson('/api/stores/programs', $programValue->toArray(), $this->headers);

        $response->assertStatus(500);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::API_ERROR,
            'code' => ApiExceptionErrorCodes::SERVER_ERROR,
            'errors' => [],
        ]);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotCreateProgramOnShopifyAuthenticationError(): void
    {
        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);
        App::context()->store->accessToken = 'invalid_access_token';

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                static::getShopifyAdminApiAuthenticationErrorResponse(),
                401
            ),
        ]);

        $response = $this->postJson('/api/stores/programs', $programValue->toArray(), $this->headers);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_LOGIN,
            'message' => 'Shopify [API] Invalid API key or access token (unrecognized login or wrong password)',
            'errors' => [],
        ]);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotCreateProgramOnShopifySellingPlanGroupCreateError(): void
    {
        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                static::getShopifyAdminApiErrorResponse(['sellingPlanGroupCreate']),
                200
            ),
        ]);

        $response = $this->postJson('/api/stores/programs', $programValue->toArray(), $this->headers);

        $response->assertStatus(500);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::API_ERROR,
            'code' => ApiExceptionErrorCodes::SERVER_ERROR,
            'message' => 'Shopify sellingPlanGroupCreate: syntax error, unexpected invalid token ("-"), expecting RCURLY at [12, 18]',
            'errors' => [],
        ]);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotCreateProgramOnShopifySellingPlanGroupCreateUserError(): void
    {
        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'currency' => $this->currentStore->currency,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                static::getShopifySellingPlanGroupCreateUserErrorResponse(),
                200
            ),
        ]);

        $response = $this->postJson('/api/stores/programs', $programValue->toArray(), $this->headers);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'Shopify sellingPlanGroupCreate: checkout charge value must be equal to or larger than 0 when checkout_charge_type is PRICE',
            'errors' => [],
        ]);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    #[DataProvider('programConfigurationsProvider')]
    public function testItCreatesProgram(
        int $tryPeriodDays,
        DepositType $depositType,
        int $depositValue,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupCreateSuccessResponse()),
        ]);

        Event::fake([
            ProgramSavedEvent::class,
        ]);

        $programValue = ProgramValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'try_period_days' => $tryPeriodDays,
            'deposit_type' => $depositType,
            'deposit_value' => $depositValue,
            'currency' => $this->currentStore->currency,
        ]);

        $response = $this->postJson('/api/stores/programs', $programValue->toArray(), $this->headers);

        $response->assertStatus(201);

        $actualResponse = $response->decodeResponseJson();

        $expectedProgramValue = ProgramValue::builder()->withShopifySellingPlanIds()->create([
            'id' => $actualResponse['program']['id'],
            'store_id' => $this->currentStore->id,
            'try_period_days' => $tryPeriodDays,
            'deposit_type' => $depositType,
            'deposit_value' => $depositValue,
            'currency' => $this->currentStore->currency,
        ])->toArray();

        $response->assertJsonStructure([
            'program' => [
                'id',
                'name',
                'shopify_selling_plan_group_id',
                'shopify_selling_plan_id',
                'try_period_days',
                'deposit_type',
                'deposit_value',
                'currency',
                'min_tbyb_items',
                'max_tbyb_items',
            ],
        ]);
        $response->assertJsonFragment($expectedProgramValue);
        Event::assertDispatched(ProgramSavedEvent::class);
        $this->assertDatabaseHas('programs', $expectedProgramValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdatesNonExistentProgram(): void
    {
        $response = $this->putJson(
            '/api/stores/programs/non_existent_program_id',
            ['name' => 'New Name', 'min_tbyb_items' => 10],
            $this->headers,
        );

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::PROGRAM_NOT_FOUND,
            'message' => 'Program not found.',
            'errors' => [],
        ]);
    }

    public function testItDoesNotUpdateProgramOnHttpRequestError(): void
    {
        $this->partialMock(ProgramRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(['errors' => '400 error'], 400),
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        });
        $response = $this->putJson(
            '/api/stores/programs/' . $program->id,
            ['name' => 'New Name', 'min_tbyb_items' => 10],
            $this->headers
        );

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_REQUEST,
            'errors' => [],
        ]);

        $updatedProgram = $program->refresh();
        $this->assertNotEquals('New Name', $updatedProgram->name);
        $this->assertNotEquals(10, $updatedProgram->min_tbyb_items);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdateProgramOnHttpServerError(): void
    {
        $this->partialMock(ProgramRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(['errors' => '500 error'], 500),
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        });
        $response = $this->putJson(
            '/api/stores/programs/' . $program->id,
            ['name' => 'New Name', 'min_tbyb_items' => 10],
            $this->headers
        );

        $response->assertStatus(500);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::API_ERROR,
            'code' => ApiExceptionErrorCodes::SERVER_ERROR,
            'errors' => [],
        ]);

        $updatedProgram = $program->refresh();
        $this->assertNotEquals('New Name', $updatedProgram->name);
        $this->assertNotEquals(10, $updatedProgram->min_tbyb_items);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdateProgramOnShopifyAuthenticationError(): void
    {
        $this->partialMock(ProgramRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        App::context()->store->accessToken = 'invalid_access_token';

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                static::getShopifyAdminApiAuthenticationErrorResponse(),
                401
            ),
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        });
        $response = $this->putJson(
            '/api/stores/programs/' . $program->id,
            ['name' => 'New Name', 'min_tbyb_items' => 10],
            $this->headers
        );

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_LOGIN,
            'message' => 'Shopify [API] Invalid API key or access token (unrecognized login or wrong password)',
            'errors' => [],
        ]);

        $updatedProgram = $program->refresh();
        $this->assertNotEquals('New Name', $updatedProgram->name);
        $this->assertNotEquals(10, $updatedProgram->min_tbyb_items);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdateProgramOnShopifySellingPlanGroupUpdateError(): void
    {
        $this->partialMock(ProgramRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                static::getShopifyAdminApiErrorResponse(['sellingPlanGroupUpdate']),
                200
            ),
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        });
        $response = $this->putJson(
            '/api/stores/programs/' . $program->id,
            ['name' => 'New Name', 'min_tbyb_items' => 10],
            $this->headers
        );

        $response->assertStatus(500);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::API_ERROR,
            'code' => ApiExceptionErrorCodes::SERVER_ERROR,
            'message' => 'Shopify sellingPlanGroupUpdate: syntax error, unexpected invalid token ("-"), expecting RCURLY at [12, 18]',
            'errors' => [],
        ]);

        $updatedProgram = $program->refresh();
        $this->assertNotEquals('New Name', $updatedProgram->name);
        $this->assertNotEquals(10, $updatedProgram->min_tbyb_items);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdateProgramOnShopifySellingPlanGroupUpdateUserError(): void
    {
        $this->partialMock(ProgramRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                static::getShopifySellingPlanGroupUpdateUserErrorResponse(),
                200
            ),
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        });
        $response = $this->putJson(
            '/api/stores/programs/' . $program->id,
            ['name' => 'New Name', 'min_tbyb_items' => 10],
            $this->headers
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'type' => ApiExceptionTypes::REQUEST_ERROR,
            'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
            'message' => 'Shopify sellingPlanGroupUpdate: checkout charge value must be equal to or larger than 0 when checkout_charge_type is PRICE',
            'errors' => [],
        ]);

        $updatedProgram = $program->refresh();
        $this->assertNotEquals('New Name', $updatedProgram->name);
        $this->assertNotEquals(10, $updatedProgram->min_tbyb_items);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdateUnchangedProgram(): void
    {
        $this->partialMock(ShopifyProgramService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('updateTbybProgram');
        });
        $this->partialMock(ProgramRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        Event::fake([
            ProgramSavedEvent::class,
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create([
                'store_id' => $this->currentStore->id,
                'name' => 'Blackcart ABC Try',
                'currency' => $this->currentStore->currency,
                'min_tbyb_items' => 10,
            ]);
        });

        $response = $this->putJson(
            '/api/stores/programs/' . $program->id,
            ['name' => $program->name, 'min_tbyb_items' => $program->min_tbyb_items],
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'program' => [
                'id',
                'name',
                'store_id',
                'shopify_selling_plan_group_id',
                'shopify_selling_plan_id',
                'try_period_days',
                'deposit_type',
                'deposit_value',
                'currency',
                'min_tbyb_items',
                'max_tbyb_items',
                'drop_off_days',
            ],
        ]);

        // No changes to the program values
        $response->assertJsonFragment(ProgramValue::from($program)->toArray());

        Event::assertNotDispatched(ProgramSavedEvent::class);
    }

    #[DataProvider('programConfigurationsProvider')]
    public function testItUpdatesProgram(
        int $tryPeriodDays,
        DepositType $depositType,
        int $depositValue,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupUpdateSuccessResponse()),
        ]);

        Event::fake([
            ProgramSavedEvent::class,
        ]);

        $program = Program::withoutEvents(function () {
            return Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        });
        $response = $this->putJson(
            '/api/stores/programs/' . $program->id,
            [
                'try_period_days' => $tryPeriodDays,
                'deposit_type' => $depositType->value,
                'deposit_value' => $depositValue,
                'drop_off_days' => 7,
            ],
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'program' => [
                'id',
                'name',
                'store_id',
                'shopify_selling_plan_group_id',
                'shopify_selling_plan_id',
                'try_period_days',
                'deposit_type',
                'deposit_value',
                'currency',
                'min_tbyb_items',
                'max_tbyb_items',
                'drop_off_days',
            ],
        ]);

        $program->try_period_days = $tryPeriodDays;
        $program->deposit_type = $depositType->value;
        $program->deposit_value = $depositValue;
        $program->drop_off_days = 7;
        $response->assertJsonFragment(ProgramValue::from($program)->toArray());

        Event::assertDispatched(ProgramSavedEvent::class);
    }

    public function testItAddsVariants(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupAddProductVariantsSuccessResponse()),
        ]);

        $program = Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        $response = $this->postJson(
            '/api/stores/programs/' . $program->id . '/variants',
            [
                'selected_variant_ids' => [
                    'gid://shopify/ProductVariant/1',
                    'gid://shopify/ProductVariant/2',
                    'gid://shopify/ProductVariant/3',
                    'gid://shopify/ProductVariant/4',
                ],
            ],
            $this->headers
        );

        $response->assertStatus(201);
        $response->assertJsonStructure([]);
    }

    public function testItRemovesVariants(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupRemoveProductVariantsSuccessResponse()),
        ]);

        $program = Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        $response = $this->deleteJson(
            '/api/stores/programs/' . $program->id . '/variants',
            [
                'selected_variant_ids' => [
                    'gid://shopify/ProductVariant/1',
                    'gid://shopify/ProductVariant/2',
                    'gid://shopify/ProductVariant/3',
                    'gid://shopify/ProductVariant/4',
                ],
            ],
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }

    public function testVariantsInProgram(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getVariantInSellingPlanGroupResponse()),
        ]);

        $program = Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        $variants = [
            'gid://shopify/ProductVariant/1',
            'gid://shopify/ProductVariant/2',
            'gid://shopify/ProductVariant/3',
            'gid://shopify/ProductVariant/4',
        ];

        $encodedVariants = array_map('urlencode', $variants);

        $encodedVariantsString = implode(',', $encodedVariants);

        $response = $this->getJson('/api/stores/programs/' . $program->id . '/variants?shopify_variant_ids=' . $encodedVariantsString, $this->headers);

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }

    public function testItRemovesProducts(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifySellingPlanGroupRemoveProductsSuccessResponse()),
        ]);

        $program = Program::factory()->withShopifySellingPlanIds()->create(['store_id' => $this->currentStore->id]);
        $response = $this->deleteJson(
            '/api/stores/programs/' . $program->id . '/products',
            [
                'selected_product_ids' => [
                    'gid://shopify/Product/1',
                    'gid://shopify/Product/2',
                    'gid://shopify/Product/3',
                    'gid://shopify/Product/4',
                ],
            ],
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }
}
