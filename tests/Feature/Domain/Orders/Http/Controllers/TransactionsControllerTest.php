<?php

declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Http\Controllers;

use App;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Carbon\CarbonImmutable;
use Config;
use Firebase\JWT\JWT;
use Str;
use Tests\TestCase;

class TransactionsControllerTest extends TestCase
{
    private Store $currentStore;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 6, 6));

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create([
                'created_at' => CarbonImmutable::now()->subMonths(2),
            ]);
        });
        App::context(store: StoreValue::from($this->currentStore));

        Config::set('services.shopify.client_secret', 'super-secret-key');

        $this->headers = [
            'Authorization' => 'Bearer ' . JWT::encode(
                (new JwtPayload(domain: $this->currentStore->domain))->toArray(),
                config('services.shopify.client_secret'),
                'HS256'
            ),
        ];
    }

    public function testItGetsTransactions(): void
    {
        $startDatetime = CarbonImmutable::create(2024, 5, 29)->subSecond();
        $endDatetime = CarbonImmutable::create(2024, 6, 3);

        $expectedTransactions = Transaction::withoutEvents(function () {
            $transactions = [];

            $transactions[] = Transaction::factory()->create([
                'kind' => TransactionKind::SALE,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 5, 29),
                'status' => 'success',
                'shop_amount' => 10000,
                'customer_amount' => 10000,
            ]);
            $transactions[] = Transaction::factory()->capture()->create([
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 5, 30),
                'status' => 'success',
                'shop_amount' => 20000,
                'customer_amount' => 20000,
            ]);
            Transaction::factory()->capture()->create([
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 5, 30),
                'status' => 'failure',
                'shop_amount' => 20000,
                'customer_amount' => 20000,
            ]);
            Transaction::factory()->authorization()->create([
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 5, 31),
                'status' => 'success',
                'shop_amount' => 30000,
                'customer_amount' => 30000,
            ]);
            $transactions[] = Transaction::factory()->create([
                'kind' => TransactionKind::REFUND,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 1),
                'status' => 'success',
                'shop_amount' => 40000,
                'customer_amount' => 40000,
            ]);
            Transaction::factory()->create([
                'kind' => TransactionKind::VOID,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 2),
                'status' => 'success',
                'shop_amount' => 50000,
                'customer_amount' => 50000,
            ]);
            Transaction::factory()->create([
                'kind' => TransactionKind::SALE,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 3),
                'status' => 'success',
                'shop_amount' => 60000,
                'customer_amount' => 60000,
            ]);

            return $transactions;
        });

        $response = $this->getJson(
            '/api/stores/orders/transactions?start=' . $startDatetime->toDateTimeString() . '&end=' . $endDatetime->toDateTimeString(),
            $this->headers
        );

        $response->assertStatus(200);
        $responseJson = $response->json();
        $transactions = collect($responseJson['transactions']);

        $this->assertCount(count($expectedTransactions), $transactions);

        foreach ($expectedTransactions as $i => $expectedTransaction) {
            $this->assertEquals(Str::replace('gid://shopify/Order/', '', $expectedTransaction->source_order_id), $transactions[$i]['order_number']);
        }
        $this->assertEquals(100 + 200, $responseJson['summary']['total_payments']);
        $this->assertEquals(-400, $responseJson['summary']['total_refunds']);

        $this->assertNull($response->headers->get('Content-Disposition'));
    }

    public function testItGetsTransactionsNoDates(): void
    {
        $expectedTransactions = Transaction::withoutEvents(function () {
            $transactions = [];

            Transaction::factory()->create([
                'kind' => TransactionKind::SALE,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 5, 29),
                'status' => 'success',
                'shop_amount' => 10000,
                'customer_amount' => 10000,
            ]);
            Transaction::factory()->capture()->create([
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 5, 30),
                'status' => 'success',
                'shop_amount' => 20000,
                'customer_amount' => 20000,
            ]);
            Transaction::factory()->authorization()->create([
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 5, 31),
                'status' => 'success',
                'shop_amount' => 30000,
                'customer_amount' => 30000,
            ]);
            $transactions[] = Transaction::factory()->create([
                'kind' => TransactionKind::REFUND,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 1),
                'status' => 'success',
                'shop_amount' => 40000,
                'customer_amount' => 40000,
            ]);
            Transaction::factory()->create([
                'kind' => TransactionKind::REFUND,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 1),
                'status' => 'failure',
                'shop_amount' => 40000,
                'customer_amount' => 40000,
            ]);
            Transaction::factory()->create([
                'kind' => TransactionKind::VOID,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 2),
                'status' => 'success',
                'shop_amount' => 50000,
                'customer_amount' => 50000,
            ]);
            $transactions[] = Transaction::factory()->create([
                'kind' => TransactionKind::SALE,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 2),
                'status' => 'success',
                'shop_amount' => 60000,
                'customer_amount' => 60000,
            ]);
            $transactions[] = Transaction::factory()->create([
                'kind' => TransactionKind::CAPTURE,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 2),
                'status' => 'success',
                'shop_amount' => 70000,
                'customer_amount' => 70000,
            ]);
            $transactions[] = Transaction::factory()->create([
                'kind' => TransactionKind::REFUND,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 2),
                'status' => 'success',
                'shop_amount' => 80000,
                'customer_amount' => 80000,
            ]);
            Transaction::factory()->create([
                'kind' => TransactionKind::SALE,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 6),
                'status' => 'success',
                'shop_amount' => 90000,
                'customer_amount' => 90000,
            ]);
            Transaction::factory()->create([
                'kind' => TransactionKind::CAPTURE,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::create(2024, 6, 6),
                'status' => 'success',
                'shop_amount' => 100000,
                'customer_amount' => 100000,
            ]);

            return $transactions;
        });

        $response = $this->getJson(
            '/api/stores/orders/transactions',
            $this->headers
        );

        $response->assertStatus(200);
        $responseJson = $response->json();
        $transactions = collect($responseJson['transactions']);

        $this->assertCount(count($expectedTransactions), $transactions);

        foreach ($expectedTransactions as $i => $expectedTransaction) {
            $this->assertEquals(Str::replace('gid://shopify/Order/', '', $expectedTransaction->source_order_id), $transactions[$i]['order_number']);
        }
        $this->assertEquals(600 + 700, $responseJson['summary']['total_payments']);
        $this->assertEquals(-400 - 800, $responseJson['summary']['total_refunds']);

        $this->assertNull($response->headers->get('Content-Disposition'));
    }

    public function testItExportsTransactions(): void
    {
        $startDatetime = CarbonImmutable::now()->subDays(5)->subSecond();
        $endDatetime = CarbonImmutable::now();

        Transaction::withoutEvents(function () {
            $transactions = [];

            $transactions[] = Transaction::factory()->create([
                'kind' => TransactionKind::SALE,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::now()->subDays(5),
                'status' => 'success',
                'shop_amount' => 10000,
                'customer_amount' => 10000,
            ]);
            $transactions[] = Transaction::factory()->capture()->create([
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::now()->subDays(4),
                'status' => 'success',
                'shop_amount' => 20000,
                'customer_amount' => 20000,
            ]);
            Transaction::factory()->capture()->create([
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::now()->subDays(4),
                'status' => 'failure',
                'shop_amount' => 20000,
                'customer_amount' => 20000,
            ]);
            Transaction::factory()->authorization()->create([
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::now()->subDays(3),
                'status' => 'success',
                'shop_amount' => 30000,
                'customer_amount' => 30000,
            ]);
            $transactions[] = Transaction::factory()->create([
                'kind' => TransactionKind::REFUND,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::now()->subDays(2),
                'status' => 'success',
                'shop_amount' => 40000,
                'customer_amount' => 40000,
            ]);
            Transaction::factory()->create([
                'kind' => TransactionKind::VOID,
                'store_id' => $this->currentStore->id,
                'processed_at' => CarbonImmutable::now()->subDays(1),
                'status' => 'success',
                'shop_amount' => 50000,
                'customer_amount' => 50000,
            ]);

            return $transactions;
        });

        $response = $this->getJson(
            '/api/stores/orders/transactions?start=' . $startDatetime->toDateTimeString() . '&end=' . $endDatetime->toDateTimeString() . '&export=1',
            $this->headers
        );

        $response->assertStatus(200);
        $this->assertEquals('attachment; filename="transactions.csv"', $response->headers->get('Content-Disposition'));
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }
}
