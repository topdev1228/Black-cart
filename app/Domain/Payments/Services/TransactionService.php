<?php
declare(strict_types=1);

namespace App\Domain\Payments\Services;

use App;
use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Payments\Exceptions\PaymentFailedException;
use App\Domain\Payments\Jobs\ReAuthAfterExternalCaptureTransactionJob;
use App\Domain\Payments\Repositories\TransactionRepository;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Str;

class TransactionService
{
    public function __construct(
        protected ShopifyTransactionService $shopifyTransactionService,
        protected TransactionRepository $transactionRepository
    ) {
    }

    public function save(TransactionValue $transaction): TransactionValue
    {
        return $this->transactionRepository->save($transaction);
    }

    public function getBySourceId(string $sourceId): TransactionValue
    {
        return $this->transactionRepository->getBySourceId($sourceId);
    }

    public function getLatestTransaction(string $orderId, TransactionKind $kind = null): TransactionValue
    {
        return $this->transactionRepository->getLatestTransaction($orderId, $kind);
    }

    public function getTransactionsFromSourceJob(string $sourceJobId, string $sourceOrderId): Collection
    {
        return $this->shopifyTransactionService->getTransactionsFromJob($sourceJobId, $sourceOrderId);
    }

    public function getTransactionBySourceIdAndSourceOrderId(string $sourceTransactionId, string $sourceOrderId): Collection
    {
        return $this->shopifyTransactionService->getTransactionByIdAndOrderId($sourceTransactionId, $sourceOrderId);
    }

    public function saveTransactionFromJob(string $jobId, OrderValue $order): TransactionValue
    {
        $transactions = $this->getTransactionsFromSourceJob($jobId, $order->sourceId);

        $transaction = $transactions
            ->where('kind', TransactionKind::AUTHORIZATION->name)
            ->sortByDateDesc('createdAt')
            ->firstOrFail();

        // If the latest Authorization wasn't successful, throw an exception
        if ($transaction['status'] !== TransactionStatus::SUCCESS->name) {
            throw new PaymentFailedException('Transaction failed');
        }

        $shopMoney = $transaction['amountSet']['shopMoney'];
        $presentmentMoney = $transaction['amountSet']['presentmentMoney'];

        $authorizationExpiresAt = $transaction['authorizationExpiresAt'];
        if ($transaction['authorizationExpiresAt'] === null) {
            // We observed that Paypal transactions have a null expiration date, so we assume it to be 7 days
            $authorizationExpiresAt = CarbonImmutable::now()->addDays(7);
        }

        return $this->save(TransactionValue::from([
            'orderId' => $order->id,
            'storeId' => App::context()->store->id,
            'sourceOrderId' => $order->sourceId,
            'sourceId' => $transaction['id'],
            'kind' => TransactionKind::tryFrom(Str::lower($transaction['kind'])),
            'status' => TransactionStatus::tryFrom(Str::lower($transaction['status'])),
            'authorizationExpiresAt' => $authorizationExpiresAt,
            'transactionSourceName' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
            'shopAmount' => Money::of($shopMoney['amount'], $shopMoney['currencyCode']),
            'shopCurrency' => CurrencyAlpha3::from($shopMoney['currencyCode']),
            'customerAmount' => Money::of($presentmentMoney['amount'], $presentmentMoney['currencyCode']),
            'customerCurrency' => CurrencyAlpha3::from($presentmentMoney['currencyCode']),
        ]));
    }

    public function createCaptureTransaction(TransactionValue $transaction): ?TransactionValue
    {
        if ($transaction->kind !== TransactionKind::CAPTURE) {
            return null;
        }

        // These are the transaction IDs from the order domain
        $transaction->id = null;
        $transaction->parentTransactionId = null;

        // Keep the source transaction name if it already exists
        try {
            $existingTransaction = $this->getBySourceId($transaction->sourceId);
            $transaction->transactionSourceName = $existingTransaction->transactionSourceName;
        } catch (ModelNotFoundException $e) {
        }

        try {
            $parentTransaction = $this->getBySourceId($transaction->parentTransactionSourceId);
        } catch (ModelNotFoundException $e) {
            return $this->save($transaction);
        }

        $transaction->parentTransactionId = $parentTransaction->id;
        $newTransaction = $this->save($transaction);

        $parentTransaction->capturedTransactionId = $newTransaction->id;
        $parentTransaction->capturedTransactionSourceId = $newTransaction->sourceId;
        $this->save($parentTransaction);

        if ($transaction->transactionSourceName !== TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART
            && $parentTransaction->transactionSourceName === TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART) {
            // There is a capture transaction that's not triggered by Blackcart, we need to re-auth the order's
            // remaining balance to guarantee the funds are available for the rest of the try period
            ReAuthAfterExternalCaptureTransactionJob::dispatch($transaction->orderId)
                ->delay(now()->addMinutes(10));
        }

        return $newTransaction;
    }
}
