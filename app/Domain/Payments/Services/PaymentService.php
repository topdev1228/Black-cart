<?php
declare(strict_types=1);

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Enums\OrderStatus;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Payments\Events\CheckoutAuthorizationSuccessEvent;
use App\Domain\Payments\Events\InitialAuthFailedEvent;
use App\Domain\Payments\Events\PaymentCompleteEvent;
use App\Domain\Payments\Events\ReAuthFailedEvent;
use App\Domain\Payments\Events\ReAuthSuccessEvent;
use App\Domain\Payments\Exceptions\PaymentFailedException;
use App\Domain\Payments\Exceptions\PaymentMandateNotFoundException;
use App\Domain\Payments\Exceptions\ShopifyMandatePaymentOutstandingAmountZeroException;
use App\Domain\Payments\Exceptions\ShopifyMandatePaymentRetryFailureLimitReachedException;
use App\Domain\Payments\Jobs\AuthExpiryNotificationJob;
use App\Domain\Payments\Jobs\ReAuthJob;
use App\Domain\Payments\Mail\ReAuthNotice;
use App\Domain\Payments\Repositories\TransactionRepository;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\Transaction;
use Brick\Money\Money;
use Exception;
use Feature;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    public function __construct(
        protected ShopifyOrderService $shopifyOrderService,
        protected ShopifyPaymentService $shopifyPaymentService,
        protected TransactionService $transactionService,
        protected BlackcartOrderService $blackcartOrderService,
    ) {
    }

    public function captureOrCreatePayment(string $orderId, string $sourceOrderId, Money $amount): ?Transaction
    {
        if ($amount->isLessThanOrEqualTo(Money::zero($amount->getCurrency()))) {
            return null;
        }

        try {
            $auth = $this->transactionService->getLatestTransaction($orderId, TransactionKind::AUTHORIZATION);
            if ($auth->capturedTransactionId === null) {
                $transaction = $this->transactionService->getTransactionBySourceIdAndSourceOrderId($auth->sourceId, $sourceOrderId);
                $authorizedAmount = Money::of(
                    $transaction->pull('totalUnsettledSet.presentmentMoney.amount'),
                    $transaction->pull('totalUnsettledSet.presentmentMoney.currencyCode')
                );
                if ($authorizedAmount->isGreaterThanOrEqualTo($amount)) {
                    $result = $this->shopifyPaymentService->capturePayment($sourceOrderId, $auth->sourceId, $amount);
                    if ($result['status'] === TransactionStatus::SUCCESS->name) {
                        $transaction = $this->transactionService->save(Transaction::from([
                            'order_id' => $auth->orderId,
                            'store_id' => $auth->storeId,
                            'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
                            'source_order_id' => $sourceOrderId,
                            'source_id' => $result['id'],
                            'kind' => TransactionKind::fromValue($result['kind']),
                            'status' => TransactionStatus::fromValue($result['status']),
                            'shop_amount' => $result['amountSet']['shopMoney']['amount'],
                            'shop_currency' => $result['amountSet']['shopMoney']['currencyCode'],
                            'customer_amount' => $result['amountSet']['presentmentMoney']['amount'],
                            'customer_currency' => $result['amountSet']['presentmentMoney']['currencyCode'],
                            'parent_transaction_id' => $auth->id,
                        ]));

                        $auth->capturedTransactionId = $transaction->id;
                        $this->transactionService->save($auth);
                        PaymentCompleteEvent::dispatch($sourceOrderId);

                        return $transaction;
                    }

                    throw new PaymentFailedException('Capture payment failed for order ID: ' . $orderId);
                }
            }
        } catch (ModelNotFoundException) {
        }

        // if the auth is not found or not captured, create a new payment
        $payment = $this->getPaymentData($sourceOrderId);

        try {
            $job = $this->shopifyPaymentService->createMandatePayment($sourceOrderId, $payment['paymentMandateId'], autoCapture: true, amount: $amount);
        } catch (ShopifyMandatePaymentOutstandingAmountZeroException|ShopifyMandatePaymentRetryFailureLimitReachedException) {
            // Order is already settled OR
            // We can no longer charge against the payment mandate because we've retried and failed too many times
            PaymentCompleteEvent::dispatch($sourceOrderId, true);

            return null;
        }

        $transactions = $this->transactionService->getTransactionsFromSourceJob($job['jobId'], $sourceOrderId);
        $result = $transactions
            ->where('kind', TransactionKind::SALE->name)
            ->sortByDateDesc('createdAt')
            ->first();
        if ($result['status'] === TransactionStatus::SUCCESS->name) {
            $transaction = $this->transactionService->save(Transaction::from([
                'order_id' => $orderId,
                'store_id' => App::context()->store->id,
                'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
                'source_order_id' => $sourceOrderId,
                'source_id' => $result['id'],
                'kind' => TransactionKind::fromValue($result['kind']),
                'status' => TransactionStatus::fromValue($result['status']),
                'shop_amount' => $result['amountSet']['shopMoney']['amount'],
                'shop_currency' => $result['amountSet']['shopMoney']['currencyCode'],
                'customer_amount' => $result['amountSet']['presentmentMoney']['amount'],
                'customer_currency' => $result['amountSet']['presentmentMoney']['currencyCode'],
            ]));
            PaymentCompleteEvent::dispatch($sourceOrderId);

            return $transaction;
        }
        throw new PaymentFailedException('Create payment failed for order ID: ' . $orderId);
    }

    public function verifyPayment(string $jobId, string $paymentReferenceId, string $sourceOrderId): bool
    {
        $job = $this->shopifyPaymentService->getPaymentAttemptJob($jobId, $paymentReferenceId, $sourceOrderId);

        if ($job['done'] === false) {
            return false;
        }

        if ($job['status'] === PaymentStatus::PURCHASED->value) {
            PaymentCompleteEvent::dispatch($sourceOrderId);

            return true;
        }

        throw new PaymentFailedException($job['errorMessage'] ?? 'Payment failed');
    }

    /**
     * @return array{paymentMandateId: string}
     */
    protected function getPaymentData(string $sourceOrderId): array
    {
        $collection = $this->shopifyPaymentService->getOrderPaymentDetails($sourceOrderId);
        $shopifyOrder = $collection['data']['order'];

        $paymentMandateId = $this->getPaymentMandate($shopifyOrder);

        return [
            'paymentMandateId' => $paymentMandateId,
        ];
    }

    protected function getPaymentMandate(Collection $order): string
    {
        // Find the Payment Mandate (Vaulted Payment Method) ID
        $paymentMandateId = $order->get('paymentCollectionDetails')?->get('vaultedPaymentMethods')?->first()['id'] ?? null;
        if ($paymentMandateId === null) {
            throw new PaymentMandateNotFoundException();
        }

        return $paymentMandateId;
    }

    public function createAuthHold(OrderValue $order): ?Transaction
    {
        if (in_array($order->status, [OrderStatus::CANCELLED, OrderStatus::COMPLETED], true)) {
            return null;
        }

        if ($order->outstandingCustomerAmount->isLessThanOrEqualTo(Money::zero($order->customerCurrency->value))) {
            return null;
        }

        $payment = $this->getPaymentData($order->sourceId);

        $amount = $order->outstandingCustomerAmount;
        $checkArchived = false;
        if (App::context()->store->ecommercePlatformPlan !== 'shopify_plus') {
            $amount = null;
            $checkArchived = !$this->shopifyOrderService->isOrderArchived($order->sourceId);
        }

        $jobId = $this->shopifyPaymentService->createMandatePayment(
            $order->sourceId,
            $payment['paymentMandateId'],
            autoCapture: false,
            amount: $amount,
        )['jobId'];

        $transaction = $this->transactionService->saveTransactionFromJob($jobId, $order);

        // Order was archived by the auth due to a Shopify bug, open it back up
        try {
            if ($checkArchived && $this->shopifyOrderService->isOrderArchived($order->sourceId)) {
                $this->shopifyOrderService->openOrder($order->sourceId);
            }
        } catch (Exception $e) {
            Log::error('Unarchive order error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'source_id' => $order->sourceId,
                'store_id' => App::context()->store->domain,
            ]);
            // Not the end of the world if the order can't be re-opened
        }

        return $transaction;
    }

    public function createInitialAuthHold(OrderValue $order): ?Transaction
    {
        // Get latest outstanding customer amount in the order
        $order = $this->blackcartOrderService->getOrderById($order->id);

        $transaction = $this->createAuthHold($order);

        CheckoutAuthorizationSuccessEvent::dispatch($order->id, $order->sourceId);

        // $transaction is null if the order is completed or has a zero outstanding balance.
        // If that's the case, there's no need to re-auth further
        if ($transaction !== null) {
            AuthExpiryNotificationJob::dispatch($order, $transaction->sourceId)->delay($transaction->authorizationExpiresAt->subDays(2));
            ReAuthJob::dispatch($order, $transaction)->delay($transaction->authorizationExpiresAt->subDay());
        }

        return $transaction;
    }

    public function triggerInitialAuthHoldFailure(string $orderId): void
    {
        InitialAuthFailedEvent::dispatch($orderId);
    }

    public function createReAuthHold(OrderValue $order): ?Transaction
    {
        if (Feature::enabled('shopify-perm-b-kill-reauth')) {
            return null;
        }

        // Get latest outstanding customer amount in the order
        $order = $this->blackcartOrderService->getOrderById($order->id);

        try {
            $transaction = $this->createAuthHold($order);
        } catch (Exception $e) {
            Log::error('Re-auth error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'source_id' => $order->sourceId,
                'store_id' => App::context()->store->domain,
            ]);

            $transaction = $this->captureOrCreatePayment($order->id, $order->sourceId, $order->outstandingCustomerAmount);

            // transaction is null if the order's outstanding amount is 0
            if ($transaction === null) {
                PaymentCompleteEvent::dispatch($order->sourceId, true);

                return null;
            }

            ReAuthFailedEvent::dispatch(Date::now()->addDay(), $order->outstandingCustomerAmount, $order->sourceId);

            return $transaction;
        }

        if ($transaction === null) {
            // No need to acquire auth hold as order's outstanding amount is 0
            return null;
        }

        ReAuthSuccessEvent::dispatch($transaction->customerAmount, $order->sourceId);

        AuthExpiryNotificationJob::dispatch($order, $transaction->sourceId)->delay($transaction->authorizationExpiresAt->subDays(2));
        ReAuthJob::dispatch($order, $transaction)->delay($transaction->authorizationExpiresAt->subDay());

        return $transaction;
    }

    public function createReAuthHoldNoCaptureOnFailure(string $orderId): ?Transaction
    {
        if (Feature::enabled('shopify-perm-b-kill-reauth')) {
            return null;
        }

        // Get latest outstanding customer amount in the order
        $order = $this->blackcartOrderService->getOrderById($orderId);

        $transaction = $this->createAuthHold($order);
        if ($transaction === null) {
            // No need to acquire auth hold as order's outstanding amount is 0
            return null;
        }

        ReAuthSuccessEvent::dispatch($transaction->customerAmount, $order->sourceId);
        ReAuthJob::dispatch($order, $transaction)->delay($transaction->authorizationExpiresAt->subDay());

        return $transaction;
    }

    public function sendAuthExpiryNotification(OrderValue $order, string $transactionSourceId)
    {
        if (Feature::enabled('shopify-perm-b-kill-reauth')) {
            return;
        }

        // TODO: make this a store setting to be exposed on Shoipfy Blackcart Admin
        if (Feature::enabled('shopify-perm-b-merchant-reauth-notice-email')) {
            // Merchant is sending this email
            return;
        }

        // Don't send the re-auth notification if:
        // the previous transaction has already been captured; or

        $transaction = $this->transactionService->getBySourceId($transactionSourceId);
        if ($transaction->capturedTransactionId !== null) {
            return;
        }

        // the amount to we'd be authing exceeds the outstanding capture amount
        $shopifyTransactionResponse = $this->shopifyPaymentService->getTransaction($order->sourceId, $transaction->sourceId);
        $shopifyTransactionCustomerAmount = Money::of(
            $shopifyTransactionResponse->pull('totalUnsettledSet.presentmentMoney.amount'),
            $shopifyTransactionResponse->pull('totalUnsettledSet.presentmentMoney.currencyCode')
        );

        if ($shopifyTransactionCustomerAmount->isGreaterThanOrEqualTo($transaction->customerAmount)
            && $transaction->customerAmount->isGreaterThan(Money::zero($transaction->customerCurrency->value))) {
            Mail::to($order->customerEmail())->send(new ReAuthNotice($order, $order->outstandingCustomerAmount, $transaction->authorizationExpiresAt));
        }
    }
}
