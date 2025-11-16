<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Enums\DepositType;
use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Repositories\LineItemRepository;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Values\Collections\LineItemCollection;
use App\Domain\Orders\Values\LineItem;
use App\Domain\Orders\Values\Order;
use App\Domain\Orders\Values\Program;
use App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Brick\Money\Money;
use Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class LineItemService
{
    public function __construct(
        protected ShopifyGraphqlService $shopifyGraphqlService,
        protected OrderRepository $orderRepository,
        protected LineItemRepository $lineItemRepository,
        protected TrialService $trialService,
    ) {
    }

    public function getBySourceId(string $sourceId): LineItem
    {
        return $this->lineItemRepository->getBySourceId($sourceId);
    }

    public function getByStatus(string $orderId, LineItemStatus $status): LineItemCollection
    {
        return $this->lineItemRepository->getByStatus($orderId, $status);
    }

    public function save(LineItem $lineItem): LineItem
    {
        return $this->lineItemRepository->save($lineItem);
    }

    /**
     * Returns an array of graphql selling plan data, indexed by the lineItem GID for convenience
     *
     * if item is TBYB:
     *
     * gid://shopify/LineItem/12878423883915 => [
     *  'id' => 'gid://shopify/LineItem/12878423883915',
     *  'sellingPLan' => [
     *      'sellingPlanId' => 'gid://shopify/LineItem/12878423883915',
     *      'name' => '5-day Try Before You Buy trial'
     *  ]
     * ]
     *
     * if item is not TBYB:
     *
     * gid://shopify/LineItem/12878423851147 => [
     *  'sellingPlan' => null
     * ]
     *
     * It may be possible for a gid to be null, or not set at all. This should be assumed as "not tbyb"
     *
     * @throws ShopifyAuthenticationException
     * @throws ShopifyClientException
     * @throws ShopifyServerException
     */
    public function fetchSellingPlans(string $orderGid): array
    {
        return Cache::remember('line-item-selling-plans-' . $orderGid, 3600, function () use ($orderGid) {
            $query = <<<QUERY
                query {
                  order(id: "$orderGid"){
                    lineItems (first: 50) {
                      edges {
                        node {
                          id,
                          sellingPlan {
                            sellingPlanId,
                            name
                          }
                        }
                      }
                    },
                    paymentTerms {
                      id,
                      paymentTermsName
                    }
                  }
                }
            QUERY;

            $sellingPlanData = $this->shopifyGraphqlService->post($query);
            $sellingPlans = [];
            foreach ($sellingPlanData['data']['order']['lineItems']['edges'] as $edge) {
                $sellingPlans[$edge['node']['id']] = $edge['node'];
            }

            return $sellingPlans;
        });
    }

    public function generateFromOrderData(Collection $orderData, string $shopifyOrderId)
    {
        $sellingPlanLineItemData = $this->fetchSellingPlans($orderData['admin_graphql_api_id']);

        $lineItems = [];
        foreach ($orderData['line_items'] as $lineItem) {
            $isTbyb = !empty($sellingPlanLineItemData[$lineItem['admin_graphql_api_id']]['sellingPlan']);
            $sellingPlanId = $isTbyb ? $sellingPlanLineItemData[$lineItem['admin_graphql_api_id']]['sellingPlan']['sellingPlanId'] : null;

            $shopPriceSet = $lineItem['price_set']['shop_money'] ?? [];
            $shopCurrency = $shopPriceSet['currency_code'] ?? CurrencyAlpha3::US_Dollar;
            $customerPriceSet = $lineItem['price_set']['presentment_money'] ?? [];
            $customerCurrency = $customerPriceSet['currency_code'] ?? CurrencyAlpha3::US_Dollar;

            $shopAmount = Money::of($shopPriceSet['amount'] ?? 0, $shopCurrency);
            $customerAmount = Money::of($customerPriceSet['amount'] ?? 0, $customerCurrency);

            $quantity = $lineItem['quantity'] ?? 1;

            $lineItems[] = LineItem::from([
                'id' => null,
                'source_order_id' => $shopifyOrderId,
                'source_id' => (string) $lineItem['admin_graphql_api_id'],
                'source_product_id' => Str::shopifyGid($lineItem['product_id'], 'Product'),
                'source_variant_id' => Str::shopifyGid($lineItem['variant_id'], 'Variant'),
                'product_title' => $lineItem['title'],
                'variant_title' => $lineItem['variant_title'],
                'thumbnail' => null, // TODO: fetch from Shopify
                'quantity' => $lineItem['quantity'],
                'original_quantity' => $lineItem['quantity'],
                'status' => LineItemStatus::OPEN,
                'line_item_data' => $lineItem,
                'is_tbyb' => $isTbyb,
                'selling_plan_id' => $sellingPlanId,
                'shop_currency' => $shopCurrency,
                'customer_currency' => $customerCurrency,
                'price_shop_amount' => $shopAmount,
                'price_customer_amount' => $customerAmount,
                'total_price_shop_amount' => $shopAmount->multipliedBy($quantity),
                'total_price_customer_amount' => $customerAmount->multipliedBy($quantity),
                'discount_shop_amount' => collect($lineItem['discount_allocations'])->reduce(function ($carry, $item) {
                    return $carry->plus($item['amount_set']['shop_money']['amount']);
                }, Money::zero($shopCurrency)),
                'discount_customer_amount' => collect($lineItem['discount_allocations'])->reduce(function ($carry, $item) {
                    return $carry->plus($item['amount_set']['presentment_money']['amount']);
                }, Money::zero($customerCurrency)),
                'tax_shop_amount' => collect($lineItem['tax_lines'])->reduce(function (Money $carry, $item) {
                    return $carry->plus($item['price_set']['shop_money']['amount']);
                }, Money::zero($shopCurrency)),
                'tax_customer_amount' => collect($lineItem['tax_lines'])->reduce(function (Money $carry, $item) {
                    return $carry->plus($item['price_set']['presentment_money']['amount']);
                }, Money::zero($customerCurrency)),
            ]);
        }

        return LineItem::collection($lineItems);
    }

    public function syncCollectionToOrder(LineItemCollection $lineItems, Order $order): void
    {
        $program = $this->getProgram();

        foreach ($lineItems as $lineItem) {
            /**
             * @var LineItem $lineItem
             */
            $lineItem->orderId = $order->id;

            if ($lineItem->isTbyb && $program && $program->shopifySellingPlanId === $lineItem->sellingPlanId) {
                $lineItem->depositType = $program->depositType;
                $lineItem->depositValue = $program->depositValue;
                if ($lineItem->depositType === DepositType::PERCENTAGE) {
                    $lineItem->depositShopAmount = $lineItem->priceShopAmount
                        ->multipliedBy($lineItem->depositValue / 100, config('money.rounding'))
                        ->multipliedBy($lineItem->quantity, config('money.rounding'));
                    $lineItem->depositCustomerAmount = $lineItem->priceCustomerAmount
                        ->multipliedBy($lineItem->depositValue / 100, config('money.rounding'))
                        ->multipliedBy($lineItem->quantity, config('money.rounding'));
                } else {
                    $lineItem->depositShopAmount = Money::ofMinor($lineItem->depositValue, $lineItem->shopCurrency->value)
                        ->multipliedBy($lineItem->quantity, config('money.rounding'));

                    $fxRate = $lineItem->priceCustomerAmount->getAmount()->toFloat() /
                        $lineItem->priceShopAmount->getAmount()->toFloat();
                    $lineItem->depositCustomerAmount = Money::ofMinor($lineItem->depositValue, $lineItem->customerCurrency->value)
                        ->multipliedBy($fxRate, config('money.rounding'))
                        ->multipliedBy($lineItem->quantity, config('money.rounding'));
                }
            }

            $lineItem = $this->lineItemRepository->save($lineItem);

            // this will move to events
            if ($lineItem->isTbyb) {
                $this->trialService->initiateTrial($lineItem, $program?->tryPeriodDays ?? TrialService::DEFAULT_TRIAL_DAYS);
            }
        }
    }

    public function cancelForOrder(Order $order): void
    {
        if (empty($order->lineItems)) {
            return;
        }

        foreach ($order->lineItems as $lineItem) {
            $this->trialService->cancelTrial($lineItem);
        }
    }

    public function getProgram(): ?Program
    {
        $response = Http::get('http://localhost:8080/api/stores/programs');

        $programData = $response['programs'][0] ?? null;

        return $programData ? Program::from($programData) : null;
    }

    public function setDecisionStatus(LineItem $lineItem, LineItemDecisionStatus $status): LineItem
    {
        $lineItem->decisionStatus = $status;

        return $this->lineItemRepository->save($lineItem);
    }

    public function adjustQuantity(LineItem $lineItem, int $removeQuantity = 0, int $addQuantity = 0): LineItem
    {
        $lineItem->quantity -= $removeQuantity;
        $lineItem->quantity += $addQuantity;

        $lineItem = $this->save($lineItem);

        if ($lineItem->quantity === 0) {
            $lineItem = $this->cancel($lineItem);
        }

        return $lineItem;
    }

    public function cancel(LineItem $lineItem)
    {
        $lineItem->status = $lineItem->status !== LineItemStatus::INTERNAL ? LineItemStatus::CANCELLED : LineItemStatus::INTERNAL_CANCELLED;

        $lineItem = $this->save($lineItem);

        $this->trialService->cancelTrial($lineItem);

        return $lineItem;
    }
}
