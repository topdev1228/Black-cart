<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Repositories\TransactionRepository;
use App\Domain\Orders\Values\Collections\TransactionCollection;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\Transaction;
use App\Domain\Orders\Values\Transaction as TransactionValue;
use App\Domain\Orders\Values\WebhookOrderTransactionsCreate;
use Carbon\CarbonImmutable;

class TransactionService
{
    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected ShopifyTransactionService $shopifyTransactionService,
    ) {
    }

    public function firstOrCreate(TransactionValue $transactionValue): TransactionValue
    {
        return $this->transactionRepository->firstOrCreate($transactionValue);
    }

    public function getBySourceId(string $sourceId): TransactionValue
    {
        return $this->transactionRepository->getBySourceId($sourceId);
    }

    public function createFromWebhook(WebhookOrderTransactionsCreate $shopifyWebhookTransaction, OrderValue $order): TransactionValue
    {
        $shopifyTransaction = $this->shopifyTransactionService->getById($shopifyWebhookTransaction->adminGraphqlApiId);

        $transactionData = [
            'store_id' => $order->storeId,
            'order_id' => $order->id,
            'source_id' => $shopifyWebhookTransaction->adminGraphqlApiId,
            'source_order_id' => $order->sourceId,
            'kind' => $shopifyWebhookTransaction->kind,
            'gateway' => $shopifyWebhookTransaction->gateway,
            'payment_id' => $shopifyWebhookTransaction->paymentId,
            'status' => $shopifyWebhookTransaction->status,
            'test' => $shopifyWebhookTransaction->test,
            'transaction_data' => $shopifyWebhookTransaction->toArray(),
            'shop_currency' => $order->shopCurrency, // the webhook doesn't provide shop currency and amount
            'customer_currency' => $shopifyWebhookTransaction->currency, // currency in the webhook is the presentment (customer) currency
            'customer_amount' => $shopifyWebhookTransaction->amount, // amount in the webhook is the presentment (customer) amount
            'unsettled_shop_amount' => $shopifyWebhookTransaction->totalUnsettledSet->shopMoney->amount,
            'unsettled_customer_amount' => $shopifyWebhookTransaction->totalUnsettledSet->presentmentMoney->amount,
            'transaction_source_name' => $shopifyWebhookTransaction->sourceName,
            'user_id' => $shopifyWebhookTransaction->userId,
            'processed_at' => $shopifyWebhookTransaction->processedAt,
            'message' => $shopifyWebhookTransaction->message,
            'error_code' => $shopifyWebhookTransaction->errorCode,
        ];

        if (!empty($shopifyTransaction)) {
            $transactionData['shop_currency'] = $shopifyTransaction['shop_currency'];
            $transactionData['shop_amount'] = $shopifyTransaction['shop_amount'];
            $transactionData['customer_currency'] = $shopifyTransaction['customer_currency'];
            $transactionData['customer_amount'] = $shopifyTransaction['customer_amount'];

            if ($shopifyWebhookTransaction->kind === TransactionKind::AUTHORIZATION) {
                // We observed that Paypal transactions have a null expiration date, so we assume it to be 7 days from
                // the processed_at date, or if that's null, then 7 days from now.
                if ($shopifyTransaction['authorization_expires_at']) {
                    $transactionData['authorization_expires_at'] = $shopifyTransaction['authorization_expires_at'];
                } elseif ($shopifyWebhookTransaction->processedAt) {
                    $transactionData['authorization_expires_at'] = $shopifyWebhookTransaction->processedAt->addDays(7);
                } else {
                    $transactionData['authorization_expires_at'] = CarbonImmutable::now()->addDays(7);
                }
            }

            if ($shopifyTransaction['parent_transaction_source_id']) {
                // The Transaction value object has a parentTransactionId property that references Blackcart's internal
                // transaction ID. It is not available in the ShopifyTransactionService, so we need to look it up here
                // and set it to protect the integrity of the data.
                $parentTransaction = $this->getBySourceId($shopifyTransaction['parent_transaction_source_id']);

                $transactionData['parent_transaction_source_id'] = $parentTransaction->sourceId;
                $transactionData['parent_transaction_id'] = $parentTransaction->id;
            }
        }

        return $this->firstOrCreate(TransactionValue::from($transactionData));
    }

    public function fetchAndSaveTransactionsForOrder(OrderValue $order): TransactionCollection
    {
        $transactions = $this->shopifyTransactionService->getByOrder($order);

        $transactionsCreated = [];
        foreach ($transactions as $transaction) {
            if (!empty($transaction->parentTransactionSourceId)) {
                // The Transaction value object has a parentTransactionId property that references Blackcart's internal
                // transaction ID. It is not available in the ShopifyTransactionService, so we need to look it up here
                // and set it to protect the integrity of the data.
                $parentTransaction = $this->getBySourceId($transaction->parentTransactionSourceId);
                $transaction->parentTransactionId = $parentTransaction->id;
            }

            $transactionsCreated[] = $this->firstOrCreate($transaction);
        }

        return TransactionValue::collection($transactionsCreated);
    }

    /**
     * @param TransactionKind|array<TransactionKind>|null $transactionKind
     */
    public function getLatestTransaction(string $orderId, TransactionKind|array|null $transactionKind = null): Transaction
    {
        return $this->transactionRepository->getLatestTransaction($orderId, $transactionKind);
    }

    /**
     * @param TransactionKind|array<TransactionKind>|null $transactionKind
     */
    public function getByOrderId(string $orderId, TransactionKind|array|null $transactionKind = null): TransactionCollection
    {
        return $this->transactionRepository->getByOrderId($orderId, $transactionKind);
    }

    public function getTransactionsProcessedAtDatetimeRangeAndKinds(
        CarbonImmutable $startDatetime,
        CarbonImmutable $endDatetime,
        TransactionKind|array|null $transactionKinds = null
    ): TransactionCollection {
        return $this->transactionRepository->getByProcessedAtDatetimeRangeAndKinds($startDatetime, $endDatetime, $transactionKinds);
    }
}
