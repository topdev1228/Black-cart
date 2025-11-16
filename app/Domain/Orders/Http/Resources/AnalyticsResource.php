<?php
declare(strict_types=1);

namespace App\Domain\Orders\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property AnalyticsResource $resource
 */
class AnalyticsResource extends JsonResource
{
    public static $wrap = 'analytics';

    public function toArray(Request $request): array
    {
        $response = [];
        foreach ($this->data as $record) {
            $response[] = [
                'orderStatus' => $record->orderStatus,
                'date' => $record->date,
                'orderCount' => $record->orderCount,
                'grossSales' => $record->grossSales->getAmount()->toFloat(),
                'netSales' => $record->netSales->getAmount()->toFloat(),
                'discounts' => $record->discounts->getAmount()->toFloat(),
                'productCost' => $record->productCost->getAmount()->toFloat(),
                'fulfillmentCost' => $record->fulfillmentCost->getAmount()->toFloat(),
                'returnShippingCost' => $record->returnShippingCost->getAmount()->toFloat(),
                'paymentProcessingCost' => $record->paymentProcessingCost->getAmount()->toFloat(),
                'tbybFee' => $record->tbybFee->getAmount()->toFloat(),
                'returns' => $record->returns->getAmount()->toFloat(),
                'profitContribution' => $record->profitContribution->getAmount()->toFloat(),
                'paidAdvertisingCost' => $record->paidAdvertisingCost->getAmount()->toFloat(),

            ];
        }

        return ['data' => $response];
    }
}
