<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Jobs;

use App;
use App\Domain\Payments\Jobs\AuthExpiryNotificationJob;
use App\Domain\Payments\Repositories\TransactionRepository;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Services\ShopifyPaymentService;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AuthExpiryNotificationJobTest extends TestCase
{
    protected StoreValue $currentStore;
    protected OrderValue $order;

    protected TransactionValue $transaction;
    protected Collection $shopifyPaymentResponse;
    protected TransactionRepository $transactionRepository;
    protected ShopifyPaymentService $shopifyPaymentService;

    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currentStore = StoreValue::from(Store::withoutEvents(function () {
            return Store::factory()->create();
        }));
        App::context(store: $this->currentStore);

        $this->paymentService = $this->mock(PaymentService::class);

        $this->order = OrderValue::builder()->create([
            'storeId' => $this->currentStore->id,
        ]);

        $this->transaction = TransactionValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'order_id' => $this->order->id,
            'customer_amount' => 500, // major?
        ]);
    }

    public function testItCallsPaymentService(): void
    {
        $this->paymentService->shouldReceive('sendAuthExpiryNotification')->once();

        AuthExpiryNotificationJob::dispatch($this->order, $this->transaction->sourceId);
    }
}
