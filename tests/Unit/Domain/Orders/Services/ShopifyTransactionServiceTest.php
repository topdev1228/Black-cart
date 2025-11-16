<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Services\ShopifyTransactionService;
use App\Domain\Shared\Exceptions\Shopify\ShopifyQueryServerException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Stores\Models\Store;
use Brick\Money\Money;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyGetTransactionResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class ShopifyTransactionServiceTest extends TestCase
{
    use ShopifyGetTransactionResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $currentStore;
    protected ShopifyTransactionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        $this->service = resolve(ShopifyTransactionService::class);
    }

    #[DataProvider('shopifyErrorExceptionsProvider')]
    public function testItDoesNotGetTransactionByIdOnShopifyError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        // We catch all Shopify exceptions and throw InternalShopifyRequestException instead
        $this->expectException($expectedException);

        $this->service->getById('gid://shopify/OrderTransaction/1');

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetsShopifyTransactionByIdClientError(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getTransactionClientErrorResponse()),
        ]);
        $this->expectException(ShopifyQueryServerException::class);

        $this->service->getById('gid://shopify/OrderTransaction/5488058826881');

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetsShopifyTransactionByIdNotFound(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getTransactionNotFoundSuccessResponse()),
        ]);

        $transaction = $this->service->getById('gid://shopify/OrderTransaction/5488058826881');
        $this->assertEmpty($transaction);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItAddsTagsMockShopifyGraphqlService(): void
    {
        $this->mock(ShopifyGraphqlService::class)
            ->shouldReceive('post')
            ->with(
                <<<'QUERY'
                    query ($id: ID!) {
                      node(id: $id) {
                        ...on OrderTransaction {
                          id
                          kind
                          authorizationExpiresAt
                          amountSet {
                            shopMoney {
                              amount
                              currencyCode
                            }
                            presentmentMoney {
                              amount
                              currencyCode
                            }
                          }
                          parentTransaction {
                            id
                          }
                        }
                      }
                    }
                    QUERY,
                [
                    'id' => 'gid://shopify/OrderTransaction/5488058826881',
                ]
            )
            ->once()
            ->andReturn($this->getSaleTransactionSuccessResponse());

        $service = resolve(ShopifyTransactionService::class);
        $service->getById('gid://shopify/OrderTransaction/5488058826881');
    }

    public function testItGetsShopifyTransactionByIdAuthorization(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getAuthorizationTransactionSuccessResponse()),
        ]);

        $transaction = $this->service->getById('gid://shopify/OrderTransaction/5488058826881');
        $this->assertEquals('gid://shopify/OrderTransaction/5488058826881', $transaction['id']);
        $this->assertEquals(TransactionKind::AUTHORIZATION, $transaction['kind']);
        $this->assertEquals(CurrencyAlpha3::Canadian_Dollar, $transaction['shop_currency']);
        $this->assertEquals(Money::ofMinor(69838, $transaction['shop_currency']->value), $transaction['shop_amount']);
        $this->assertEquals(CurrencyAlpha3::US_Dollar, $transaction['customer_currency']);
        $this->assertEquals(Money::ofMinor(51680, $transaction['customer_currency']->value), $transaction['customer_amount']);
        $this->assertEquals(Date::parse('2024-03-13T21:52:55Z'), $transaction['authorization_expires_at']);
        $this->assertNull($transaction['parent_transaction_source_id']);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetsShopifyTransactionByIdSale(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getSaleTransactionSuccessResponse()),
        ]);

        $transaction = $this->service->getById('gid://shopify/OrderTransaction/5488058630273');
        $this->assertEquals('gid://shopify/OrderTransaction/5488058630273', $transaction['id']);
        $this->assertEquals(TransactionKind::SALE, $transaction['kind']);
        $this->assertEquals(CurrencyAlpha3::Canadian_Dollar, $transaction['shop_currency']);
        $this->assertEquals(Money::ofMinor(69838, $transaction['shop_currency']->value), $transaction['shop_amount']);
        $this->assertEquals(CurrencyAlpha3::US_Dollar, $transaction['customer_currency']);
        $this->assertEquals(Money::ofMinor(51680, $transaction['customer_currency']->value), $transaction['customer_amount']);
        $this->assertNull($transaction['authorization_expires_at']);
        $this->assertNull($transaction['parent_transaction_source_id']);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItGetsShopifyTransactionByIdCapture(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getCaptureTransactionSuccessResponse()),
        ]);

        $transaction = $this->service->getById('gid://shopify/OrderTransaction/5488059121793');
        $this->assertEquals('gid://shopify/OrderTransaction/5488059121793', $transaction['id']);
        $this->assertEquals(TransactionKind::CAPTURE, $transaction['kind']);
        $this->assertEquals(CurrencyAlpha3::Canadian_Dollar, $transaction['shop_currency']);
        $this->assertEquals(Money::ofMinor(69838, $transaction['shop_currency']->value), $transaction['shop_amount']);
        $this->assertEquals(CurrencyAlpha3::US_Dollar, $transaction['customer_currency']);
        $this->assertEquals(Money::ofMinor(51680, $transaction['customer_currency']->value), $transaction['customer_amount']);
        $this->assertNull($transaction['authorization_expires_at']);
        $this->assertEquals('gid://shopify/OrderTransaction/5488058826881', $transaction['parent_transaction_source_id']);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }
}
