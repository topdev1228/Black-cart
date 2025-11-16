<?php
declare(strict_types=1);

namespace App\Domain\Orders\Http\Resources;

use App\Domain\Orders\Enums\TransactionKind;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

/**
 * @property TransactionsResource $resource
 */
class TransactionsResource extends JsonResource
{
    public static $wrap = '';

    public function toArray(Request $request): array
    {
        $response = [
            'transactions' => [],
            'summary' => [],
        ];

        $paymentsTotal = 0;
        $refundsTotal = 0;
        foreach ($this->resource as $transaction) {
            $amount = $transaction->shopAmount->getAmount()->toFloat();
            if ($transaction->kind === TransactionKind::REFUND) {
                $amount = -1 * $amount;
                $type = 'refund';
                $refundsTotal += $amount;
            } else {
                $type = 'payment';
                $paymentsTotal += $amount;
            }

            $response['transactions'][] = [
                'date' => $transaction->processedAt->toIso8601String(),
                'type' => $type,
                'order_number' => Str::replace('gid://shopify/Order/', '', $transaction->sourceOrderId), // To be replaced by the orders.name column when the value is available in the orders_transactions table
                'amount' => $amount,
            ];
        }

        $response['summary'] = [
            'total_payments' => $paymentsTotal,
            'total_refunds' => $refundsTotal,
        ];

        return $response;
    }
}
