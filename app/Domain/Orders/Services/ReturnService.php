<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\ReturnStatus;
use App\Domain\Orders\Events\ReturnCreatedEvent;
use App\Domain\Orders\Repositories\ReturnRepository;
use App\Domain\Orders\Values\OrderReturn as ReturnValue;
use App\Domain\Orders\Values\ReturnLineItem as ReturnLineItemValue;
use App\Domain\Orders\Values\WebhookReturnsApprove;
use App\Domain\Orders\Values\WebhookReturnsLineItemApprove;
use Brick\Money\Money;
use Str;

class ReturnService
{
    public function __construct(
        protected OrderService $orderService,
        protected ReturnRepository $returnRepository,
        protected ReturnLineItemService $returnLineItemService,
        protected LineItemService $lineItemService
    ) {
    }

    public function save(ReturnValue $returnValue): ReturnValue
    {
        return $this->returnRepository->save($returnValue);
    }

    public function update(ReturnValue $returnValue): ReturnValue
    {
        return $this->returnRepository->update($returnValue);
    }

    public function getBySourceId(string $sourceId): ReturnValue
    {
        return $this->returnRepository->getBySourceId($sourceId);
    }

    public function createFromWebhook(WebhookReturnsApprove $returnData): ReturnValue
    {
        $orderId = Str::shopifyGid($returnData->order->adminGraphqlApiId, 'Order');
        $order = $this->orderService->getBySourceId($orderId);

        $customerCurrency = $order->customerCurrency;
        $shopCurrency = $order->shopCurrency;

        $returnValue = ReturnValue::from([
            'store_id' => App::context()->store->id,
            'order_id' => $order->id,
            'source_id' => $returnData->adminGraphqlApiId,
            'source_order_id' => $orderId,
            'name' => $returnData->name,
            'status' => ReturnStatus::from(Str::lower($returnData->status)),
            'total_quantity' => $returnData->totalReturnLineItems,
            'return_data' => $returnData->toArray(),
            'customer_currency' => $customerCurrency,
            'shop_currency' => $shopCurrency,
            'tbyb_gross_sales_shop_amount' => Money::zero($shopCurrency->value),
            'upfront_gross_sales_shop_amount' => Money::zero($shopCurrency->value),
            'tbyb_gross_sales_customer_amount' => Money::zero($customerCurrency->value),
            'upfront_gross_sales_customer_amount' => Money::zero($customerCurrency->value),
            'tbyb_discounts_shop_amount' => Money::zero($shopCurrency->value),
            'tbyb_discounts_customer_amount' => Money::zero($customerCurrency->value),
            'upfront_discounts_shop_amount' => Money::zero($shopCurrency->value),
            'upfront_discounts_customer_amount' => Money::zero($customerCurrency->value),
            'tbyb_tax_shop_amount' => Money::zero($shopCurrency->value),
            'upfront_tax_shop_amount' => Money::zero($shopCurrency->value),
            'tbyb_tax_customer_amount' => Money::zero($customerCurrency->value),
            'upfront_tax_customer_amount' => Money::zero($customerCurrency->value),
            'tbyb_total_shop_amount' => Money::zero($shopCurrency->value),
            'tbyb_total_customer_amount' => Money::zero($customerCurrency->value),
        ]);
        $returnValue = $this->save($returnValue);

        if (($returnData->returnLineItems?->count() ?? 0) > 0) {
            $returnData->returnLineItems->each(function (WebhookReturnsLineItemApprove $returnLineItem) use ($returnValue) {
                $lineItem = $this->lineItemService->getBySourceId($returnLineItem->fulfillmentLineItem->lineItem->adminGraphqlApiId);

                $discountShopAmount = $lineItem->discountShopAmount->dividedBy($returnLineItem->quantity, config('money.rounding'));
                $discountedCustomerAmount = $lineItem->discountCustomerAmount->dividedBy($returnLineItem->quantity, config('money.rounding'));
                $grossSalesShopAmount = $lineItem->priceShopAmount->minus($discountShopAmount)->multipliedBy($returnLineItem->quantity, config('money.rounding'));
                $grossSalesCustomerAmount = $lineItem->priceCustomerAmount->minus($discountedCustomerAmount)->multipliedBy($returnLineItem->quantity, config('money.rounding'));
                $taxShopAmount = $lineItem->taxShopAmount->dividedBy($returnLineItem->quantity)->multipliedBy($returnLineItem->quantity, config('money.rounding'));
                $taxCustomerAmount = $lineItem->taxCustomerAmount->dividedBy($returnLineItem->quantity)->multipliedBy($returnLineItem->quantity, config('money.rounding'));
                $discountShopAmount = $discountShopAmount->multipliedBy($returnLineItem->quantity, config('money.rounding'));
                $discountedCustomerAmount = $discountedCustomerAmount->multipliedBy($returnLineItem->quantity, config('money.rounding'));

                if ($lineItem->isTbyb) {
                    $returnValue->tbybGrossSalesShopAmount = $returnValue->tbybGrossSalesShopAmount->plus($grossSalesShopAmount);
                    $returnValue->tbybGrossSalesCustomerAmount = $returnValue->tbybGrossSalesCustomerAmount->plus($grossSalesCustomerAmount);
                    $returnValue->tbybDiscountsShopAmount = $returnValue->tbybDiscountsShopAmount->plus($discountShopAmount);
                    $returnValue->tbybDiscountsCustomerAmount = $returnValue->tbybDiscountsCustomerAmount->plus($discountedCustomerAmount);
                    $returnValue->tbybTaxShopAmount = $returnValue->tbybTaxShopAmount->plus($taxShopAmount);
                    $returnValue->tbybTaxCustomerAmount = $returnValue->tbybTaxCustomerAmount->plus($taxCustomerAmount);
                    $returnValue->tbybTotalShopAmount = $returnValue->tbybTotalShopAmount->plus($grossSalesShopAmount)->plus($taxShopAmount);
                    $returnValue->tbybTotalCustomerAmount = $returnValue->tbybTotalCustomerAmount->plus($grossSalesCustomerAmount)->plus($taxCustomerAmount);
                } else {
                    $returnValue->upfrontGrossSalesShopAmount = $returnValue->upfrontGrossSalesShopAmount->plus($grossSalesShopAmount);
                    $returnValue->upfrontGrossSalesCustomerAmount = $returnValue->upfrontGrossSalesCustomerAmount->plus($grossSalesCustomerAmount);
                    $returnValue->upfrontDiscountsShopAmount = $returnValue->upfrontDiscountsShopAmount->plus($discountShopAmount);
                    $returnValue->upfrontDiscountsCustomerAmount = $returnValue->upfrontDiscountsCustomerAmount->plus($discountedCustomerAmount);
                    $returnValue->upfrontTaxShopAmount = $returnValue->upfrontTaxShopAmount->plus($taxShopAmount);
                    $returnValue->upfrontTaxCustomerAmount = $returnValue->upfrontTaxCustomerAmount->plus($taxCustomerAmount);
                }

                $returnLineItemValue = ReturnLineItemValue::from([
                    'order_return_id' => $returnValue->id,
                    'source_id' => $returnLineItem->adminGraphqlApiId,
                    'source_return_id' => $returnValue->sourceId,
                    'line_item_id' => $returnLineItem->fulfillmentLineItem->lineItem->adminGraphqlApiId,
                    'quantity' => $returnLineItem->quantity,
                    'return_reason' => $returnLineItem->returnReason,
                    'return_reason_note' => $returnLineItem->returnReasonNote,
                    'customer_note' => $returnLineItem->customerNote,
                    'return_line_item_data' => $returnLineItem->toArray(),
                    'shop_currency' => $returnValue->shopCurrency,
                    'customer_currency' => $returnValue->customerCurrency,
                    'gross_sales_shop_amount' => $grossSalesShopAmount,
                    'gross_sales_customer_amount' => $grossSalesCustomerAmount,
                    'discounts_shop_amount' => $discountShopAmount,
                    'discounts_customer_amount' => $discountedCustomerAmount,
                    'tax_shop_amount' => $taxShopAmount,
                    'tax_customer_amount' => $taxCustomerAmount,
                    'is_tbyb' => $lineItem->isTbyb,
                ]);
                $this->returnLineItemService->save($returnLineItemValue);
            });
            $returnValue = $this->save($returnValue);
            ReturnCreatedEvent::dispatch($returnValue);
        }

        return $returnValue;
    }
}
