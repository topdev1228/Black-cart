<?php
declare(strict_types=1);

namespace App\Domain\Programs\Services;

use App\Domain\Programs\Enums\DepositType;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Shared\Services\ShopifyGraphqlService;

class ShopifyProgramService
{
    protected const SHIPPING_DELAY = 5;

    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function create(ProgramValue $programValue): ProgramValue
    {
        $mutation = 'sellingPlanGroupCreate';
        $freeWord = $this->getFreeWord($programValue->depositValue);
        $checkoutChargeLine = $this->getCheckoutChargeLine(
            $programValue->depositType,
            $programValue->depositValue,
        );

        $daysAfterCheckout = $programValue->tryPeriodDays + self::SHIPPING_DELAY;

        $queryString = <<<QUERY
        mutation {
          {$mutation}(
            input: {
              name: "{$programValue->name}"
              merchantCode: "try-before-you-buy"
              options: [
                "Try before you buy"
              ]
              position: 1
              sellingPlansToCreate: [
                {
                  name: "{$programValue->name} {$programValue->tryPeriodDays}-day{$freeWord} trial"
                  category: TRY_BEFORE_YOU_BUY
                  options: [
                    "Try{$freeWord} for {$programValue->tryPeriodDays} days"
                  ]
                  billingPolicy: {
                    fixed: {
                      {$checkoutChargeLine}
                      remainingBalanceChargeTrigger: TIME_AFTER_CHECKOUT
                      remainingBalanceChargeTimeAfterCheckout: "P{$daysAfterCheckout}D"
                    }
                  }
                  deliveryPolicy: {fixed: {fulfillmentTrigger: UNKNOWN}}
                  inventoryPolicy: {reserve: ON_SALE}
                }
              ]
            }
          ) {
            sellingPlanGroup {
              id
              sellingPlans(first: 1) {
                edges {
                  node {
                    id
                    name
                    options
                    billingPolicy {
                      ... on SellingPlanFixedBillingPolicy {
                        checkoutCharge {
                          type
                          value {
                            ... on SellingPlanCheckoutChargePercentageValue {
                              percentage
                            }
                            ... on MoneyV2 {
                              amount
                              currencyCode
                            }
                          }
                        }
                        remainingBalanceChargeExactTime
                        remainingBalanceChargeTimeAfterCheckout
                        remainingBalanceChargeTrigger
                      }
                    }
                    deliveryPolicy {
                      ... on SellingPlanFixedDeliveryPolicy {
                        anchors {
                          cutoffDay
                          day
                          month
                          type
                        }
                        cutoff
                        fulfillmentExactTime
                        fulfillmentTrigger
                        intent
                        preAnchorBehavior
                      }
                    }
                    inventoryPolicy {
                      ... on SellingPlanInventoryPolicy {
                        reserve
                      }
                    }
                  }
                }
              }
            }
            userErrors {
              field
              message
            }
          }
        }
        QUERY;

        $response = $this->shopifyGraphqlService->postMutation($queryString);

        return new ProgramValue(
            storeId: $programValue->storeId,
            name: $programValue->name,
            shopifySellingPlanGroupId: $response['data']['sellingPlanGroupCreate']['sellingPlanGroup']['id'],
            shopifySellingPlanId: $response['data']['sellingPlanGroupCreate']['sellingPlanGroup']['sellingPlans']['edges'][0]['node']['id'],
            tryPeriodDays: $programValue->tryPeriodDays,
            depositType: $programValue->depositType,
            depositValue: $programValue->depositValue,
            currency: $programValue->currency,
            minTbybItems: $programValue->minTbybItems,
            maxTbybItems: $programValue->maxTbybItems,
            dropOffDays: $programValue->dropOffDays
        );
    }

    public function update(ProgramValue $updatedProgramValue): void
    {
        $mutation = 'sellingPlanGroupUpdate';
        $freeWord = $this->getFreeWord($updatedProgramValue->depositValue);
        $checkoutChargeLine = $this->getCheckoutChargeLine(
            $updatedProgramValue->depositType,
            $updatedProgramValue->depositValue,
        );

        $daysAfterCheckout = $updatedProgramValue->tryPeriodDays + self::SHIPPING_DELAY;

        $queryString = <<<QUERY
        mutation {
          {$mutation}(
            id: "{$updatedProgramValue->shopifySellingPlanGroupId}",
            input: {
              name: "{$updatedProgramValue->name}"
              sellingPlansToUpdate: [
                {
                  id: "{$updatedProgramValue->shopifySellingPlanId}",
                  name: "{$updatedProgramValue->name} {$updatedProgramValue->tryPeriodDays}-day{$freeWord} trial"
                  options: [
                    "Try{$freeWord} for {$updatedProgramValue->tryPeriodDays} days"
                  ]
                  billingPolicy: {
                    fixed: {
                      {$checkoutChargeLine}
                      remainingBalanceChargeTimeAfterCheckout: "P{$daysAfterCheckout}D"
                    }
                  }
                }
              ]
            }
          ) {
            sellingPlanGroup {
              id
              name
              sellingPlans(first: 1) {
                edges {
                  node {
                    id
                    name
                    options
                    billingPolicy {
                      ... on SellingPlanFixedBillingPolicy {
                        checkoutCharge {
                          type
                          value {
                            ... on SellingPlanCheckoutChargePercentageValue {
                              percentage
                            }
                            ... on MoneyV2 {
                              amount
                              currencyCode
                            }
                          }
                        }
                        remainingBalanceChargeExactTime
                        remainingBalanceChargeTimeAfterCheckout
                        remainingBalanceChargeTrigger
                      }
                    }
                    deliveryPolicy {
                      ... on SellingPlanFixedDeliveryPolicy {
                        anchors {
                          cutoffDay
                          day
                          month
                          type
                        }
                        cutoff
                        fulfillmentExactTime
                        fulfillmentTrigger
                        intent
                        preAnchorBehavior
                      }
                    }
                    inventoryPolicy {
                      ... on SellingPlanInventoryPolicy {
                        reserve
                      }
                    }
                  }
                }
              }
            }
            userErrors {
              field
              message
            }
          }
        }
        QUERY;

        $this->shopifyGraphqlService->postMutation($queryString);
    }

    private function getFreeWord(int $deposit): string
    {
        return $deposit > 0 ? '' : ' free';
    }

    private function getCheckoutChargeLine(DepositType $depositType, int $deposit): string
    {
        if ($depositType === DepositType::PERCENTAGE) {
            return "checkoutCharge: {type: PERCENTAGE, value: {percentage: $deposit}}";
        }

        $depositValue = number_format((float) $deposit / 100, 2);

        return "checkoutCharge: {type: PRICE, value: {fixedValue: $depositValue}}";
    }
}
