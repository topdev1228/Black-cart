<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Orders\Traits;

trait ShopifyGetTransactionsByOrderIdResponsesTestData
{
    public static function getShopifyGetTransactionsByOrderIdSuccessResponse(): array
    {
        return [
            'data' => [
                'order' => [
                    'id' => 'gid://shopify/Order/4172347572353',
                    'transactions' => [
                        [
                            'id' => 'gid://shopify/OrderTransaction/5472548814977',
                            'gateway' => 'shopify_payments',
                            'kind' => 'SALE',
                            'paymentId' => 'r0z6dNuqZvMb9o7eWrUxumbFu',
                            'status' => 'SUCCESS',
                            'test' => true,
                            'authorizationExpiresAt' => null,
                            'receiptJson' => ['id' => 'pi_3Oj8hTS6NiBIzaGR1fwefI5Q', 'object' => 'payment_intent', 'amount' => 32440, 'amount_capturable' => 0, 'amount_received' => 32440, 'canceled_at' => null, 'cancellation_reason' => null, 'capture_method' => 'automatic', 'charges' => ['object' => 'list', 'data' => [['id' => 'ch_3Oj8hTS6NiBIzaGR1EXtuf9c', 'object' => 'charge', 'amount' => 32440, 'application_fee' => 'fee_1Oj8hUS6NiBIzaGRJHhxqjMp', 'balance_transaction' => ['id' => 'txn_3Oj8hTS6NiBIzaGR1Cesi3oT', 'object' => 'balance_transaction', 'exchange_rate' => 1.34518], 'captured' => true, 'created' => 1707779652, 'currency' => 'usd', 'failure_code' => null, 'failure_message' => null, 'fraud_details' => [], 'livemode' => false, 'metadata' => ['email' => 'matthew@blackcart.com', 'manual_entry' => 'false', 'order_id' => 'r0z6dNuqZvMb9o7eWrUxumbFu', 'order_transaction_id' => '5472548814977', 'payments_charge_id' => '1876476067969', 'shop_id' => '56807391361', 'shop_name' => 'blackcart-matthew-teststore2', 'transaction_fee_tax_amount' => '0', 'transaction_fee_total_amount' => '1770'], 'outcome' => ['network_status' => 'approved_by_network', 'reason' => null, 'risk_level' => 'normal', 'risk_score' => 38, 'seller_message' => 'Payment complete.', 'type' => 'authorized'], 'paid' => true, 'payment_intent' => 'pi_3Oj8hTS6NiBIzaGR1fwefI5Q', 'payment_method' => 'pm_1Oj8hTS6NiBIzaGRqjvRgR3b', 'payment_method_details' => ['card' => ['amount_authorized' => 32440, 'brand' => 'visa', 'checks' => ['address_line1_check' => 'pass', 'address_postal_code_check' => 'pass', 'cvc_check' => 'pass'], 'country' => 'US', 'description' => 'Visa Classic', 'ds_transaction_id' => null, 'exp_month' => 3, 'exp_year' => 2045, 'extended_authorization' => ['status' => 'disabled'], 'fingerprint' => 'KE6OIQsc8EspGDeW', 'funding' => 'credit', 'iin' => '424242', 'incremental_authorization' => ['status' => 'unavailable'], 'installments' => null, 'issuer' => 'Stripe Payments UK Limited', 'last4' => '4242', 'mandate' => null, 'moto' => null, 'multicapture' => ['status' => 'unavailable'], 'network' => 'visa', 'network_token' => ['used' => false], 'network_transaction_id' => '756599531007111', 'overcapture' => ['maximum_amount_capturable' => 32440, 'status' => 'unavailable'], 'payment_account_reference' => 'xR4eIL3CZCjH9hMkhqeysHPkE5Zxs', 'three_d_secure' => null, 'wallet' => null], 'type' => 'card'], 'refunded' => false, 'source' => null, 'status' => 'succeeded', 'mit_params' => ['network_transaction_id' => '756599531007111']]], 'has_more' => false, 'total_count' => 1, 'url' => "\/v1\/charges?payment_intent=pi_3Oj8hTS6NiBIzaGR1fwefI5Q"], 'confirmation_method' => 'manual', 'created' => 1707779651, 'currency' => 'usd', 'last_payment_error' => null, 'livemode' => false, 'metadata' => ['email' => 'matthew@blackcart.com', 'manual_entry' => 'false', 'order_id' => 'r0z6dNuqZvMb9o7eWrUxumbFu', 'order_transaction_id' => '5472548814977', 'payments_charge_id' => '1876476067969', 'shop_id' => '56807391361', 'shop_name' => 'blackcart-matthew-teststore2', 'transaction_fee_tax_amount' => '0', 'transaction_fee_total_amount' => '1770'], 'next_action' => null, 'payment_method' => 'pm_1Oj8hTS6NiBIzaGRqjvRgR3b', 'payment_method_types' => ['card'], 'source' => null, 'status' => 'succeeded'],
                            'amountSet' => [
                                'shopMoney' => [
                                    'amount' => '436.38',
                                    'currencyCode' => 'CAD',
                                ],
                                'presentmentMoney' => [
                                    'amount' => '324.4',
                                    'currencyCode' => 'USD',
                                ],
                            ],
                            'totalUnsettledSet' => [
                                'shopMoney' => [
                                    'amount' => '0.0',
                                    'currencyCode' => 'CAD',
                                ],
                                'presentmentMoney' => [
                                    'amount' => '0.0',
                                    'currencyCode' => 'USD',
                                ],
                            ],
                            'parentTransaction' => null,
                            'processedAt' => '2024-02-12T23:14:11Z',
                            'errorCode' => null,
                        ],
                        [
                            'id' => 'gid://shopify/OrderTransaction/5472550453377',
                            'gateway' => 'shopify_payments',
                            'kind' => 'REFUND',
                            'paymentId' => '#1007.2',
                            'status' => 'SUCCESS',
                            'test' => true,
                            'authorizationExpiresAt' => null,
                            'receiptJson' => ['id' => 're_3Oj8hTS6NiBIzaGR1WR8K55o', 'amount' => 5120, 'balance_transaction' => ['id' => 'txn_3Oj8hTS6NiBIzaGR10RaceqP', 'object' => 'balance_transaction', 'exchange_rate' => 1.3566], 'charge' => ['id' => 'ch_3Oj8hTS6NiBIzaGR1EXtuf9c', 'object' => 'charge', 'amount' => 32440, 'application_fee' => 'fee_1Oj8hUS6NiBIzaGRJHhxqjMp', 'balance_transaction' => 'txn_3Oj8hTS6NiBIzaGR1Cesi3oT', 'captured' => true, 'created' => 1707779652, 'currency' => 'usd', 'failure_code' => null, 'failure_message' => null, 'fraud_details' => [], 'livemode' => false, 'metadata' => ['email' => 'matthew@blackcart.com', 'manual_entry' => 'false', 'order_id' => 'r0z6dNuqZvMb9o7eWrUxumbFu', 'order_transaction_id' => '5472548814977', 'payments_charge_id' => '1876476067969', 'shop_id' => '56807391361', 'shop_name' => 'blackcart-matthew-teststore2', 'transaction_fee_tax_amount' => '0', 'transaction_fee_total_amount' => '1770'], 'outcome' => ['network_status' => 'approved_by_network', 'reason' => null, 'risk_level' => 'normal', 'risk_score' => 38, 'seller_message' => 'Payment complete.', 'type' => 'authorized'], 'paid' => true, 'payment_intent' => 'pi_3Oj8hTS6NiBIzaGR1fwefI5Q', 'payment_method' => 'pm_1Oj8hTS6NiBIzaGRqjvRgR3b', 'payment_method_details' => ['card' => ['amount_authorized' => 32440, 'brand' => 'visa', 'checks' => ['address_line1_check' => 'pass', 'address_postal_code_check' => 'pass', 'cvc_check' => 'pass'], 'country' => 'US', 'description' => 'Visa Classic', 'ds_transaction_id' => null, 'exp_month' => 3, 'exp_year' => 2045, 'extended_authorization' => ['status' => 'disabled'], 'fingerprint' => 'KE6OIQsc8EspGDeW', 'funding' => 'credit', 'iin' => '424242', 'incremental_authorization' => ['status' => 'unavailable'], 'installments' => null, 'issuer' => 'Stripe Payments UK Limited', 'last4' => '4242', 'mandate' => null, 'moto' => null, 'multicapture' => ['status' => 'unavailable'], 'network' => 'visa', 'network_token' => ['used' => false], 'network_transaction_id' => '756599531007111', 'overcapture' => ['maximum_amount_capturable' => 32440, 'status' => 'unavailable'], 'payment_account_reference' => 'xR4eIL3CZCjH9hMkhqeysHPkE5Zxs', 'three_d_secure' => null, 'wallet' => null], 'type' => 'card'], 'refunded' => false, 'source' => null, 'status' => 'succeeded', 'mit_params' => ['network_transaction_id' => '756599531007111']], 'object' => 'refund', 'reason' => null, 'status' => 'succeeded', 'created' => 1707867204, 'currency' => 'usd', 'metadata' => ['order_transaction_id' => '5472550453377', 'payments_refund_id' => '75796676737'], 'payment_method_details' => ['card' => ['acquirer_reference_number' => null, 'acquirer_reference_number_status' => 'pending'], 'type' => 'card'], 'mit_params' => []],
                            'amountSet' => [
                                'shopMoney' => [
                                    'amount' => '69.46',
                                    'currencyCode' => 'CAD',
                                ],
                                'presentmentMoney' => [
                                    'amount' => '51.2',
                                    'currencyCode' => 'USD',
                                ],
                            ],
                            'totalUnsettledSet' => [
                                'shopMoney' => [
                                    'amount' => '0.0',
                                    'currencyCode' => 'CAD',
                                ],
                                'presentmentMoney' => [
                                    'amount' => '0.0',
                                    'currencyCode' => 'USD',
                                ],
                            ],
                            'parentTransaction' => [
                                'id' => 'gid://shopify/OrderTransaction/5472548814977',
                            ],
                            'processedAt' => '2024-02-13T23:33:24Z',
                            'errorCode' => null,
                        ],
                    ],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 3,
                    'actualQueryCost' => 3,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1997,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }

    public static function getShopifyGetCaptureTransactionsByOrderIdSuccessResponse(): array
    {
        return [
            'data' => [
                'order' => [
                    'id' => 'gid://shopify/Order/4172347572353',
                    'transactions' => [
                        [
                            'id' => 'gid://shopify/OrderTransaction/5472548814977',
                            'gateway' => 'shopify_payments',
                            'kind' => 'AUTHORIZATION',
                            'paymentId' => 'r0z6dNuqZvMb9o7eWrUxumbFu',
                            'status' => 'SUCCESS',
                            'test' => true,
                            'authorizationExpiresAt' => '2024-02-19T23:14:11Z',
                            'receiptJson' => ['id' => 'pi_3Oj8hTS6NiBIzaGR1fwefI5Q', 'object' => 'payment_intent', 'amount' => 32440, 'amount_capturable' => 0, 'amount_received' => 32440, 'canceled_at' => null, 'cancellation_reason' => null, 'capture_method' => 'automatic', 'charges' => ['object' => 'list', 'data' => [['id' => 'ch_3Oj8hTS6NiBIzaGR1EXtuf9c', 'object' => 'charge', 'amount' => 32440, 'application_fee' => 'fee_1Oj8hUS6NiBIzaGRJHhxqjMp', 'balance_transaction' => ['id' => 'txn_3Oj8hTS6NiBIzaGR1Cesi3oT', 'object' => 'balance_transaction', 'exchange_rate' => 1.34518], 'captured' => true, 'created' => 1707779652, 'currency' => 'usd', 'failure_code' => null, 'failure_message' => null, 'fraud_details' => [], 'livemode' => false, 'metadata' => ['email' => 'matthew@blackcart.com', 'manual_entry' => 'false', 'order_id' => 'r0z6dNuqZvMb9o7eWrUxumbFu', 'order_transaction_id' => '5472548814977', 'payments_charge_id' => '1876476067969', 'shop_id' => '56807391361', 'shop_name' => 'blackcart-matthew-teststore2', 'transaction_fee_tax_amount' => '0', 'transaction_fee_total_amount' => '1770'], 'outcome' => ['network_status' => 'approved_by_network', 'reason' => null, 'risk_level' => 'normal', 'risk_score' => 38, 'seller_message' => 'Payment complete.', 'type' => 'authorized'], 'paid' => true, 'payment_intent' => 'pi_3Oj8hTS6NiBIzaGR1fwefI5Q', 'payment_method' => 'pm_1Oj8hTS6NiBIzaGRqjvRgR3b', 'payment_method_details' => ['card' => ['amount_authorized' => 32440, 'brand' => 'visa', 'checks' => ['address_line1_check' => 'pass', 'address_postal_code_check' => 'pass', 'cvc_check' => 'pass'], 'country' => 'US', 'description' => 'Visa Classic', 'ds_transaction_id' => null, 'exp_month' => 3, 'exp_year' => 2045, 'extended_authorization' => ['status' => 'disabled'], 'fingerprint' => 'KE6OIQsc8EspGDeW', 'funding' => 'credit', 'iin' => '424242', 'incremental_authorization' => ['status' => 'unavailable'], 'installments' => null, 'issuer' => 'Stripe Payments UK Limited', 'last4' => '4242', 'mandate' => null, 'moto' => null, 'multicapture' => ['status' => 'unavailable'], 'network' => 'visa', 'network_token' => ['used' => false], 'network_transaction_id' => '756599531007111', 'overcapture' => ['maximum_amount_capturable' => 32440, 'status' => 'unavailable'], 'payment_account_reference' => 'xR4eIL3CZCjH9hMkhqeysHPkE5Zxs', 'three_d_secure' => null, 'wallet' => null], 'type' => 'card'], 'refunded' => false, 'source' => null, 'status' => 'succeeded', 'mit_params' => ['network_transaction_id' => '756599531007111']]], 'has_more' => false, 'total_count' => 1, 'url' => "\/v1\/charges?payment_intent=pi_3Oj8hTS6NiBIzaGR1fwefI5Q"], 'confirmation_method' => 'manual', 'created' => 1707779651, 'currency' => 'usd', 'last_payment_error' => null, 'livemode' => false, 'metadata' => ['email' => 'matthew@blackcart.com', 'manual_entry' => 'false', 'order_id' => 'r0z6dNuqZvMb9o7eWrUxumbFu', 'order_transaction_id' => '5472548814977', 'payments_charge_id' => '1876476067969', 'shop_id' => '56807391361', 'shop_name' => 'blackcart-matthew-teststore2', 'transaction_fee_tax_amount' => '0', 'transaction_fee_total_amount' => '1770'], 'next_action' => null, 'payment_method' => 'pm_1Oj8hTS6NiBIzaGRqjvRgR3b', 'payment_method_types' => ['card'], 'source' => null, 'status' => 'succeeded'],
                            'amountSet' => [
                                'shopMoney' => [
                                    'amount' => '436.38',
                                    'currencyCode' => 'CAD',
                                ],
                                'presentmentMoney' => [
                                    'amount' => '324.4',
                                    'currencyCode' => 'USD',
                                ],
                            ],
                            'totalUnsettledSet' => [
                                'shopMoney' => [
                                    'amount' => '436.38',
                                    'currencyCode' => 'CAD',
                                ],
                                'presentmentMoney' => [
                                    'amount' => '324.4',
                                    'currencyCode' => 'USD',
                                ],
                            ],
                            'parentTransaction' => null,
                            'processedAt' => '2024-02-12T23:14:11Z',
                            'errorCode' => null,
                        ],
                        [
                            'id' => 'gid://shopify/OrderTransaction/5472550453377',
                            'gateway' => 'shopify_payments',
                            'kind' => 'CAPTURE',
                            'paymentId' => '#1007.2',
                            'status' => 'SUCCESS',
                            'test' => true,
                            'authorizationExpiresAt' => null,
                            'receiptJson' => ['id' => 're_3Oj8hTS6NiBIzaGR1WR8K55o', 'amount' => 5120, 'balance_transaction' => ['id' => 'txn_3Oj8hTS6NiBIzaGR10RaceqP', 'object' => 'balance_transaction', 'exchange_rate' => 1.3566], 'charge' => ['id' => 'ch_3Oj8hTS6NiBIzaGR1EXtuf9c', 'object' => 'charge', 'amount' => 32440, 'application_fee' => 'fee_1Oj8hUS6NiBIzaGRJHhxqjMp', 'balance_transaction' => 'txn_3Oj8hTS6NiBIzaGR1Cesi3oT', 'captured' => true, 'created' => 1707779652, 'currency' => 'usd', 'failure_code' => null, 'failure_message' => null, 'fraud_details' => [], 'livemode' => false, 'metadata' => ['email' => 'matthew@blackcart.com', 'manual_entry' => 'false', 'order_id' => 'r0z6dNuqZvMb9o7eWrUxumbFu', 'order_transaction_id' => '5472548814977', 'payments_charge_id' => '1876476067969', 'shop_id' => '56807391361', 'shop_name' => 'blackcart-matthew-teststore2', 'transaction_fee_tax_amount' => '0', 'transaction_fee_total_amount' => '1770'], 'outcome' => ['network_status' => 'approved_by_network', 'reason' => null, 'risk_level' => 'normal', 'risk_score' => 38, 'seller_message' => 'Payment complete.', 'type' => 'authorized'], 'paid' => true, 'payment_intent' => 'pi_3Oj8hTS6NiBIzaGR1fwefI5Q', 'payment_method' => 'pm_1Oj8hTS6NiBIzaGRqjvRgR3b', 'payment_method_details' => ['card' => ['amount_authorized' => 32440, 'brand' => 'visa', 'checks' => ['address_line1_check' => 'pass', 'address_postal_code_check' => 'pass', 'cvc_check' => 'pass'], 'country' => 'US', 'description' => 'Visa Classic', 'ds_transaction_id' => null, 'exp_month' => 3, 'exp_year' => 2045, 'extended_authorization' => ['status' => 'disabled'], 'fingerprint' => 'KE6OIQsc8EspGDeW', 'funding' => 'credit', 'iin' => '424242', 'incremental_authorization' => ['status' => 'unavailable'], 'installments' => null, 'issuer' => 'Stripe Payments UK Limited', 'last4' => '4242', 'mandate' => null, 'moto' => null, 'multicapture' => ['status' => 'unavailable'], 'network' => 'visa', 'network_token' => ['used' => false], 'network_transaction_id' => '756599531007111', 'overcapture' => ['maximum_amount_capturable' => 32440, 'status' => 'unavailable'], 'payment_account_reference' => 'xR4eIL3CZCjH9hMkhqeysHPkE5Zxs', 'three_d_secure' => null, 'wallet' => null], 'type' => 'card'], 'refunded' => false, 'source' => null, 'status' => 'succeeded', 'mit_params' => ['network_transaction_id' => '756599531007111']], 'object' => 'refund', 'reason' => null, 'status' => 'succeeded', 'created' => 1707867204, 'currency' => 'usd', 'metadata' => ['order_transaction_id' => '5472550453377', 'payments_refund_id' => '75796676737'], 'payment_method_details' => ['card' => ['acquirer_reference_number' => null, 'acquirer_reference_number_status' => 'pending'], 'type' => 'card'], 'mit_params' => []],
                            'amountSet' => [
                                'shopMoney' => [
                                    'amount' => '69.46',
                                    'currencyCode' => 'CAD',
                                ],
                                'presentmentMoney' => [
                                    'amount' => '51.2',
                                    'currencyCode' => 'USD',
                                ],
                            ],
                            'totalUnsettledSet' => [
                                'shopMoney' => [
                                    'amount' => '0.0',
                                    'currencyCode' => 'CAD',
                                ],
                                'presentmentMoney' => [
                                    'amount' => '0.0',
                                    'currencyCode' => 'USD',
                                ],
                            ],
                            'parentTransaction' => [
                                'id' => 'gid://shopify/OrderTransaction/5472548814977',
                            ],
                            'processedAt' => '2024-02-14T23:33:24Z',
                            'errorCode' => null,
                        ],
                    ],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 3,
                    'actualQueryCost' => 3,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1997,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }

    public static function getShopifyGetTransactionsByOrderIdOrderNotFoundResponse(): array
    {
        return [
            'data' => [
                'order' => null,
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 4,
                    'actualQueryCost' => 1,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1999,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }

    public static function getShopifyGetTransactionsByOrderIdErrorResponse(): array
    {
        return [
            'errors' => [
                [
                    'message' => "Field 'ids' doesn't exist on type 'StaffMember'",
                    'locations' => [
                        [
                            'line' => 37,
                            'column' => 25,
                        ],
                    ],
                    'path' => [
                        'query',
                        'order',
                        'transactions',
                        'user',
                        'ids',
                    ],
                    'extensions' => [
                        'code' => 'undefinedField',
                        'typeName' => 'StaffMember',
                        'fieldName' => 'ids',
                    ],
                ],
            ],
        ];
    }
}
