<?php
declare(strict_types=1);

namespace App\Domain\Payments\Repositories;

use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Models\Transaction;
use App\Domain\Payments\Values\Transaction as TransactionValue;

class TransactionRepository
{
    const SOURCE_TRANSACTION_NAME_BLACKCART = 'blackcart';

    public function __construct()
    {
    }

    public function save(TransactionValue $transactionValue): TransactionValue
    {
        return TransactionValue::from(
            Transaction::updateOrCreate(
                [
                    'store_id' => $transactionValue->storeId,
                    'source_id' => $transactionValue->sourceId,
                ],
                $transactionValue->toArray()
            )
        );
    }

    public function getLatestTransaction(string $orderId, TransactionKind $kind = null): TransactionValue
    {
        if ($kind !== null) {
            return TransactionValue::from(Transaction::where('order_id', $orderId)->where('kind', $kind)->latest()->firstOrFail());
        }

        return TransactionValue::from(Transaction::where('order_id', $orderId)->latest()->firstOrFail());
    }

    public function getBySourceId(string $sourceId): TransactionValue
    {
        return TransactionValue::from(Transaction::where('source_id', $sourceId)->firstOrFail());
    }
}
