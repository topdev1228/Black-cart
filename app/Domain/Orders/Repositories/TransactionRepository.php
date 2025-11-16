<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Values\Collections\TransactionCollection;
use App\Domain\Orders\Values\Transaction as TransactionValue;
use Carbon\CarbonImmutable;

class TransactionRepository
{
    public function __construct()
    {
    }

    public function firstOrCreate(TransactionValue $transactionValue): TransactionValue
    {
        return TransactionValue::from(
            Transaction::firstOrCreate(['source_id' => $transactionValue->sourceId], $transactionValue->toArray())
        );
    }

    public function getBySourceId(string $sourceId): TransactionValue
    {
        return TransactionValue::from(Transaction::where('source_id', $sourceId)->firstOrFail());
    }

    /**
     * @param TransactionKind|array<TransactionKind>|null $transactionKind
     */
    public function getLatestTransaction(string $orderId, TransactionKind|array|null $transactionKind = null): TransactionValue
    {
        $query = Transaction::where('order_id', $orderId)->latest();
        if ($transactionKind instanceof TransactionKind) {
            $query->where('kind', $transactionKind);
        }

        if (is_array($transactionKind)) {
            $query->whereIn('kind', $transactionKind);
        }

        return TransactionValue::from($query->firstOrFail());
    }

    /**
     * @param TransactionKind|array<TransactionKind>|null $transactionKind
     */
    public function getByOrderId(string $orderId, TransactionKind|array|null $transactionKind = null): TransactionCollection
    {
        $query = Transaction::where('order_id', $orderId)->orderBy('created_at', 'desc');
        if ($transactionKind instanceof TransactionKind) {
            $query->where('kind', $transactionKind);
        }

        if (is_array($transactionKind)) {
            $query->whereIn('kind', $transactionKind);
        }

        return TransactionValue::collection($query->get());
    }

    public function getByProcessedAtDatetimeRangeAndKinds(
        CarbonImmutable $startDatetime,
        CarbonImmutable $endDatetime,
        TransactionKind|array|null $transactionKinds = null
    ): TransactionCollection {
        $query = Transaction::where('processed_at', '>=', $startDatetime)
            ->where('processed_at', '<', $endDatetime)
            ->where('status', TransactionStatus::SUCCESS)
            ->orderBy('processed_at', 'asc');
        if ($transactionKinds instanceof TransactionKind) {
            $query->where('kind', $transactionKinds);
        }
        if (is_array($transactionKinds)) {
            $query->whereIn('kind', $transactionKinds);
        }

        return TransactionValue::collection($query->get());
    }
}
