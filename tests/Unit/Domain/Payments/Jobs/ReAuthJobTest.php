<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Jobs;

use App\Domain\Payments\Jobs\ReAuthJob;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use Tests\TestCase;

class ReAuthJobTest extends TestCase
{
    public function testItCallsCreateReAuthHold(): void
    {
        $paymentService = $this->mock(PaymentService::class);

        $order = OrderValue::builder()->create();
        $transaction = TransactionValue::builder()->create();

        $paymentService->shouldReceive('createReAuthHold')->once()->with($order);

        $job = new ReAuthJob($order, $transaction);
        app()->call([$job, 'handle']);
    }
}
