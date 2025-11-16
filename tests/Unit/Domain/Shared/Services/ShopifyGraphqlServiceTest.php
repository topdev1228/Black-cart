<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Services;

use App;
use App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationServerException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryServerException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Models\Store;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShopifyGraphqlServiceTest extends TestCase
{
    public function testItPostsQueriesWithoutVariables(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response(['data' => ['test' => true]], 200),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'query { shop { name } }';

        $response = $shopifyGraphqlService->post($query);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);

        Http::assertSent(function (Request $request) {
            return isset($request->data()['query']) && !isset($request->data()['variables']);
        });
    }

    public function testItPostsMutationsWithoutVariables(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response(['data' => ['productCreate' => ['userErrors' => []]]], 200),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $queryString = 'mutation { productCreate(input: { title: "Test Product" }) { product { id } } }';
        $mutation = 'productCreate';

        $response = $shopifyGraphqlService->postMutation($queryString);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);

        Http::assertSent(function (Request $request) {
            return isset($request->data()['query']) && !isset($request->data()['variables']);
        });
    }

    public function testItPostsQueriesWithVariables(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response(['data' => ['test' => true]], 200),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'query { shop { name } }';
        $variables = ['variable1' => 'value1', 'variable2' => 'value2'];

        $response = $shopifyGraphqlService->post($query, $variables);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);

        Http::assertSent(function (Request $request) {
            return isset($request->data()['query']) && isset($request->data()['variables']);
        });
    }

    public function testItPostsMutationsWithVariables(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response(['data' => ['productCreate' => ['userErrors' => []]]], 200),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $queryString = 'mutation { productCreate(input: { title: "Test Product" }) { product { id } } }';
        $mutation = 'productCreate';
        $variables = ['variable1' => 'value1', 'variable2' => 'value2'];

        $response = $shopifyGraphqlService->postMutation($queryString, $variables);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);

        Http::assertSent(function (Request $request) {
            return isset($request->data()['query']) && isset($request->data()['variables']);
        });
    }

    public function testItClientErrorsOnHttp400Error(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response(['errors' => ['test' => true]], 400),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'query { shop { name } }';

        $this->expectException(ShopifyClientException::class);

        $shopifyGraphqlService->post($query);
    }

    public function testItAuthenticationErrorsOnHttp401Error(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response([
                'errors' => '[API] Invalid API key or access token (unrecognized login or wrong password)',
            ], 401),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'query { shop { name } }';

        $this->expectException(ShopifyAuthenticationException::class);

        $shopifyGraphqlService->post($query);
    }

    public function testItServerErrorsOnHttp401Error(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response(['errors' => ['test' => true]], 500),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'query { shop { name } }';

        $this->expectException(ShopifyServerException::class);

        $shopifyGraphqlService->post($query);
    }

    public function testItThrowServerExceptionOnQueryError(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response([
                'errors' => [
                    [
                        'message' => "Field 'foo' doesn't exist on type 'Product'",
                        'locations' => [
                            [
                                'line' => 431,
                                'column' => 17,
                            ],
                        ],
                        'path' => [
                            'query foo',
                            'products',
                            'edges',
                            'node',
                            'foo',
                        ],
                        'extensions' => [
                            'code' => 'undefinedField',
                            'typeName' => 'Product',
                            'fieldName' => 'foo',
                        ],
                    ],
                ],
            ]),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'query { shop { name } }';

        $this->expectException(ShopifyQueryServerException::class);
        $this->expectExceptionMessage('Shopify query foo->products->edges->node->foo: Field \'foo\' doesn\'t exist on type \'Product\'');

        $shopifyGraphqlService->post($query);
    }

    public function testItThrowClientExceptionOnQueryError(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response([
                'data' => [
                    'appUsageRecordCreate' => [
                        'userErrors' => [
                            [
                                'field' => null,
                                'message' => 'Validation failed: Price must be greater than zero',
                            ],
                        ],
                        'appUsageRecord' => null,
                    ],
                ],
            ]),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'query { shop { name } }';

        $this->expectException(ShopifyQueryClientException::class);

        $shopifyGraphqlService->post($query);
    }

    public function testItThrowServerExceptionOnMutationError(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response([
                'errors' => [
                    [
                        'message' => "Field 'appUsageRecordCreate' doesn't accept argument 'foo'",
                        'locations' => [
                            [
                                'line' => 400,
                                'column' => 9,
                            ],
                        ],
                        'path' => [
                            'mutation appUsageRecordCreate',
                            'appUsageRecordCreate',
                            'foo',
                        ],
                        'extensions' => [
                            'code' => 'argumentNotAccepted',
                            'name' => 'appUsageRecordCreate',
                            'typeName' => 'Field',
                            'argumentName' => 'foo',
                        ],
                    ],
                ],
            ]),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'mutation { shop { name } }';

        $this->expectException(ShopifyMutationServerException::class);
        $this->expectExceptionMessage('Shopify mutation appUsageRecordCreate->appUsageRecordCreate->foo: Field \'appUsageRecordCreate\' doesn\'t accept argument \'foo\'');

        $shopifyGraphqlService->postMutation($query);
    }

    public function testItThrowsClientExceptionOnMutationError(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        Http::fake([
            '*' => Http::response([
                'data' => [
                    'appUsageRecordCreate' => [
                        'userErrors' => [
                            [
                                'field' => null,
                                'message' => 'Validation failed: Price must be greater than zero',
                            ],
                        ],
                        'appUsageRecord' => null,
                    ],
                ],
            ]),
        ]);

        $shopifyGraphqlService = new ShopifyGraphqlService(config('services.shopify.admin_graphql_api_version'));

        $query = 'mutation { shop { name } }';

        $this->expectException(ShopifyMutationClientException::class);

        $shopifyGraphqlService->postMutation($query);
    }

    public function testItSetsApiVersion(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: $store);
        App::context(jwtToken: new JwtToken(JWT::encode(
            (new JwtPayload(domain: $store->domain))->toArray(),
            config('services.shopify.client_secret'),
            'HS256'
        )));

        $shopifyGraphqlService = resolve(ShopifyGraphqlService::class);
        Http::fake(
            [
                App::context()->store->domain . '/admin/api/2024-01/graphql.json' => Http::sequence()->push(['data' => ['test' => true]], 200),
                App::context()->store->domain . '/admin/api/unstable/graphql.json' => Http::sequence()->push(['data' => ['test' => true]], 200),
            ],
        );

        $shopifyGraphqlService->post('query { shop { name } }');

        $shopifyGraphqlService->setApiVersion('unstable');
        $shopifyGraphqlService->post('query { shop { name } }');

        Http::assertSequencesAreEmpty();
    }
}
