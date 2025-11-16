<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Jobs;

use App\Domain\Payments\Exceptions\PaymentFailedException;
use App\Domain\Payments\Jobs\PaymentAttemptJob;
use App\Domain\Payments\Services\PaymentService;
use Bus;
use Tests\TestCase;

class PaymentAttemptJobTest extends TestCase
{
    public function testItFinalizesPayments(): void
    {
        Bus::fake();

        $orderPaymentService = $this->mock(PaymentService::class);

        $jobId = 'test-job-id';
        $paymentReferenceId = 'test-payment-reference-id';
        $orderId = 'test-order-id';

        $orderPaymentService->shouldReceive('verifyPayment')->once()->with($jobId, $paymentReferenceId, $orderId)->andReturn(true);

        $job = new PaymentAttemptJob($jobId, $paymentReferenceId, $orderId);
        app()->call([$job, 'handle']);

        Bus::assertNothingDispatched();
    }

    public function testItFailsPayments(): void
    {
        Bus::fake();

        $orderPaymentService = $this->mock(PaymentService::class);

        $jobId = 'test-job-id';
        $paymentReferenceId = 'test-payment-reference-id';
        $orderId = 'test-order-id';

        $orderPaymentService->shouldReceive('verifyPayment')->once()->with($jobId, $paymentReferenceId, $orderId)->andThrow(PaymentFailedException::class);

        $job = new PaymentAttemptJob($jobId, $paymentReferenceId, $orderId);
        app()->call([$job, 'handle']);

        Bus::assertNothingDispatched();
    }

    public function testItRedispatchesJob(): void
    {
        Bus::fake();

        $orderPaymentService = $this->mock(PaymentService::class);

        $jobId = 'test-job-id';
        $paymentReferenceId = 'test-payment-reference-id';
        $orderId = 'test-order-id';

        $orderPaymentService->shouldReceive('verifyPayment')->once()->with($jobId, $paymentReferenceId, $orderId)->andReturn(false);

        $job = new PaymentAttemptJob($jobId, $paymentReferenceId, $orderId);
        app()->call([$job, 'handle']);

        Bus::assertDispatched(PaymentAttemptJob::class);
    }
}
