<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Values\Collections\TransactionCollection;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\Transaction as TransactionValue;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Brick\Money\Money;
use Illuminate\Support\Facades\Date;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Str;

class ShopifyTransactionService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function getByOrder(OrderValue $order): TransactionCollection
    {
        $queryString = /** @lang GraphQL */
            <<<'QUERY'
            query ($id: ID!) {
              order(id: $id) {
                id
                transactions(first: 20) {
                  id
                  gateway
                  kind
                  paymentId
                  status
                  test
                  authorizationExpiresAt
                  amountSet {
                    shopMoney {
                      amount
                      currencyCode
                    }
                    presentmentMoney {
                      amount
                      currencyCode
                    }
                  }
                  totalUnsettledSet {
                    shopMoney {
                      amount
                      currencyCode
                    }
                    presentmentMoney {
                      amount
                      currencyCode
                    }
                  }
                  parentTransaction {
                    id
                  }
                  processedAt
                  errorCode
                }
              }
            }
            QUERY;

        $response = $this->shopifyGraphqlService->post($queryString, [
            'id' => $order->sourceId,
        ]);

        if (is_null($response['data']['order'])) {
            return TransactionValue::collection([]);
        }

        $transactions = [];
        foreach ($response['data']['order']['transactions'] as $data) {
            $kind = TransactionKind::from(Str::lower($data['kind']));
            $shopCurrency = CurrencyAlpha3::from($data['amountSet']['shopMoney']['currencyCode']);
            $customerCurrency = CurrencyAlpha3::from($data['amountSet']['presentmentMoney']['currencyCode']);

            $transaction = [
                'store_id' => $order->storeId,
                'order_id' => $order->id,
                'source_id' => $data['id'],
                'source_order_id' => $order->sourceId,
                'kind' => $kind,
                'gateway' => $data['gateway'],
                'payment_id' => $data['paymentId'],
                'status' => TransactionStatus::from(Str::lower($data['status'])),
                'test' => boolval($data['test']),
                'transaction_data' => $data,
                'shop_currency' => $shopCurrency,
                'customer_currency' => $customerCurrency,
                'shop_amount' => Money::of(
                    $data['amountSet']['shopMoney']['amount'],
                    $shopCurrency->value,
                ),
                'customer_amount' => Money::of(
                    $data['amountSet']['presentmentMoney']['amount'],
                    $customerCurrency->value,
                ),
                'unsettled_shop_amount' => Money::of(
                    $data['totalUnsettledSet']['shopMoney']['amount'],
                    $shopCurrency->value,
                ),
                'unsettled_customer_amount' => Money::of(
                    $data['totalUnsettledSet']['presentmentMoney']['amount'],
                    $customerCurrency->value,
                ),
                'authorization_expires_at' => null,
                'parent_transaction_source_id' => null,
                'processed_at' => Date::parse($data['processedAt']),
                'error_code' => $data['errorCode'],
            ];

            if ($kind === TransactionKind::AUTHORIZATION && !empty($data['authorizationExpiresAt'])) {
                $transaction['authorization_expires_at'] = Date::parse($data['authorizationExpiresAt']);
            }
            if ($kind === TransactionKind::CAPTURE && !empty($data['parentTransaction']['id'])) {
                // The Transaction value object has a parentTransactionId property that references Blackcart's internal
                // transaction ID. It is not available here but will/should be set in the TransactionService.
                $transaction['parent_transaction_source_id'] = $data['parentTransaction']['id'];
            }

            $transactions[] = TransactionValue::from($transaction);
        }

        return TransactionValue::collection($transactions);
    }

    public function getById(string $shopifyTransactionGid): array
    {
        $queryString = /** @lang GraphQL */
            <<<'QUERY'
            query ($id: ID!) {
              node(id: $id) {
                ...on OrderTransaction {
                  id
                  kind
                  authorizationExpiresAt
                  amountSet {
                    shopMoney {
                      amount
                      currencyCode
                    }
                    presentmentMoney {
                      amount
                      currencyCode
                    }
                  }
                  parentTransaction {
                    id
                  }
                }
              }
            }
            QUERY;

        $response = $this->shopifyGraphqlService->post($queryString, [
            'id' => $shopifyTransactionGid,
        ]);

        if (isset($response['errors']) && !empty($response['errors'])) {
            throw new ShopifyClientException($response['errors'][0]['message']);
        }

        // If the transaction is not found or invalid, Shopify returns null for the data.node field
        if (is_null($response['data']['node'])) {
            return [];
        }

        $data = $response['data']['node'];
        $kind = TransactionKind::from(Str::lower($data['kind']));

        $result = [
            'id' => $data['id'],
            'kind' => $kind,
            'shop_currency' => CurrencyAlpha3::from($data['amountSet']['shopMoney']['currencyCode']),
            'shop_amount' => Money::of(
                $data['amountSet']['shopMoney']['amount'],
                $data['amountSet']['shopMoney']['currencyCode'],
            ),
            'customer_currency' => CurrencyAlpha3::from($data['amountSet']['presentmentMoney']['currencyCode']),
            'customer_amount' => Money::of(
                $data['amountSet']['presentmentMoney']['amount'],
                $data['amountSet']['presentmentMoney']['currencyCode'],
            ),
            'authorization_expires_at' => null,
            'parent_transaction_source_id' => null,
        ];

        if ($kind === TransactionKind::AUTHORIZATION && !empty($data['authorizationExpiresAt'])) {
            $result['authorization_expires_at'] = Date::parse($data['authorizationExpiresAt']);
        }
        if ($kind === TransactionKind::CAPTURE && !empty($data['parentTransaction']['id'])) {
            $result['parent_transaction_source_id'] = $data['parentTransaction']['id'];
        }

        return $result;
    }
}
