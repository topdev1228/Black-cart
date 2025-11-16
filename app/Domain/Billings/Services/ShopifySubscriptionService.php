<?php
declare(strict_types=1);

namespace App\Domain\Billings\Services;

use App;
use App\Domain\Billings\Enums\SubscriptionLineItemType;
use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\SubscriptionLineItem;
use App\Domain\Billings\Values\UsageConfig;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Str;

class ShopifySubscriptionService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function create(SubscriptionValue $subscriptionValue, string $storeDomain): SubscriptionValue
    {
        $mutation = 'appSubscriptionCreate';

        $name = 'Blackcart';

        $storeName = explode('.', $storeDomain)[0];
        $returnUrl = sprintf('https://admin.shopify.com/store/%s/apps/blackcart-tbyb', $storeName);

        $recurringTerms = '$99 every 30 days.  First $2,500 in Blackcart Try Before You Buy net sales included in subscription.  Free 30 day trial with unlimited usage.';
        $recurringAmount = Money::of(99, 'USD');
        $recurringAmountCurrency = CurrencyAlpha3::US_Dollar;

        $appUsageTerms = 'The first $2,500 in Blackcart Try Before You Buy net sales included in subscription. Then pay $100 for every additional $2,500 in Try Before You Buy net sales. Unlimited usage during the 30 day free trial.';
        $usageCappedAmount = Money::of(2500, 'USD');
        $usageCappedAmountCurrency = CurrencyAlpha3::US_Dollar;

        $isTest = (!App::environment('production')) ? 'true' : 'false';

        $queryString = <<<QUERY
            mutation {
              {$mutation}(
                name: "{$name}",
                returnUrl: "{$returnUrl}",
                trialDays: 30,
                test: {$isTest},
                lineItems: [
                  {
                    plan: {
                      appRecurringPricingDetails: {
                        price: {
                          amount: {$recurringAmount->getAmount()->toFloat()},
                          currencyCode: {$recurringAmountCurrency->value}
                        }
                      }
                    }
                  },
                  {
                    plan: {
                      appUsagePricingDetails: {
                        terms: "{$appUsageTerms}",
                        cappedAmount: {
                          amount: {$usageCappedAmount->getAmount()->toFloat()},
                          currencyCode: {$usageCappedAmountCurrency->value}
                        }
                      }
                    }
                  }
                ]
              ) {
                userErrors {
                  field,
                  message
                },
                confirmationUrl,
                appSubscription {
                  id,
                  currentPeriodEnd,
                  status,
                  test,
                  trialDays,
                  lineItems {
                    id,
                    plan {
                      pricingDetails {
                        __typename
                      }
                    }
                  }
                }
              }
            }
            QUERY;

        $response = $this->shopifyGraphqlService->postMutation($queryString);

        $shopifySubscription = $response['data'][$mutation]['appSubscription'];
        $shopifySubscriptionLineItems = $shopifySubscription['lineItems'];

        $lineItems = Collection::empty();
        foreach ($shopifySubscriptionLineItems as $shopifySubscriptionLineItem) {
            if ($shopifySubscriptionLineItem['plan']['pricingDetails']['__typename'] === 'AppRecurringPricing') {
                $lineItems->push(SubscriptionLineItem::from([
                    'shopify_app_subscription_id' => $shopifySubscription['id'],
                    'shopify_app_subscription_line_item_id' => $shopifySubscriptionLineItem['id'],
                    'type' => SubscriptionLineItemType::RECURRING,
                    'terms' => $recurringTerms,
                    'recurring_amount' => $recurringAmount,
                    'recurring_amount_currency' => $recurringAmountCurrency,
                    'usage_capped_amount' => Money::of(0, 'USD'), // default 0 values
                    'usage_capped_amount_currency' => CurrencyAlpha3::US_Dollar, // default 0 values
                ]));

                continue;
            }

            $lineItems->push(SubscriptionLineItem::from([
                'shopify_app_subscription_id' => $shopifySubscription['id'],
                'shopify_app_subscription_line_item_id' => $shopifySubscriptionLineItem['id'],
                'type' => SubscriptionLineItemType::USAGE,
                'terms' => $appUsageTerms,
                'recurring_amount' => Money::of(0, 'USD'), // default 0 values
                'recurring_amount_currency' => CurrencyAlpha3::US_Dollar, // default 0 values
                'usage_capped_amount' => $usageCappedAmount,
                'usage_capped_amount_currency' => $usageCappedAmountCurrency,
            ]));
        }

        /** @psalm-suppress InvalidArgument */
        return SubscriptionValue::from([
            'store_id' => $subscriptionValue->storeId,
            'shopify_app_subscription_id' => $shopifySubscription['id'],
            'shopify_confirmation_url' => $response['data'][$mutation]['confirmationUrl'],
            'status' => SubscriptionStatus::tryFrom(Str::lower($shopifySubscription['status'])),
            'current_period_end' => is_null($shopifySubscription['currentPeriodEnd']) ? null :
                Date::createFromFormat('Y-m-dTH:i:sZ', $shopifySubscription['currentPeriodEnd']),
            'trial_days' => $shopifySubscription['trialDays'],
            'is_test' => $shopifySubscription['test'],
            'subscription_line_items' => SubscriptionLineItem::collection($lineItems),
        ]);
    }

    public function addUsage(string $description, Money $price, UsageConfig $usageConfig): void
    {
        $queryString = /** @lang GraphQL */
            <<<'QUERY'
            mutation appUsageRecordCreate($description: String!, $amount: Decimal!, $currency: CurrencyCode!, $subscriptionLineItemId: ID!) {
              appUsageRecordCreate(
                description: $description,
                price: {
                    amount: $amount,
                    currencyCode: $currency
                },
                subscriptionLineItemId: $subscriptionLineItemId
              ) {
                userErrors {
                  field,
                  message
                },
                appUsageRecord {
                    id
                    createdAt
                    subscriptionLineItem {
                        id
                    }
                }
              }
            }
            QUERY;

        $variables = [
            'description' => $description,
            'amount' => $price->getAmount()->toFloat(),
            'currency' => $price->getCurrency()->getCurrencyCode(),
            'subscriptionLineItemId' => $usageConfig->subscriptionLineItemId,
        ];

        $this->shopifyGraphqlService->postMutation($queryString, $variables);
    }

    public function getCurrentPeriodEndByAppSubscriptionId(string $shopifyAppSubscriptionId): ?CarbonImmutable
    {
        $queryString = /** @lang GraphQL */
            <<<'QUERY'
            query ($id: ID!) {
              node(id: $id) {
                ...on AppSubscription {
                  id
                  currentPeriodEnd
                }
              }
            }
            QUERY;

        $variables = [
            'id' => $shopifyAppSubscriptionId,
        ];

        $response = $this->shopifyGraphqlService->post($queryString, $variables);

        // If the subscription is not found or invalid, Shopify returns null for the data.node field
        if (is_null($response['data']['node'])) {
            return null;
        }

        if (!isset($response['data']['node']['currentPeriodEnd'])) {
            return null;
        }

        return new CarbonImmutable($response['data']['node']['currentPeriodEnd']);
    }
}
