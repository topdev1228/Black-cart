<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Orders\Traits;

trait ShopifyUpdatePaymentTermsResponsesTestData
{
    public static function getShopifyUpdatePaymentTermsSuccessResponse(): array
    {
        return [
            'data' => [
                'paymentTermsUpdate' => [
                    'userErrors' => [],
                ],
            ],
        ];
    }

    public static function getShopifyUpdatePaymentTermsOnPaidOrderErrorResponse(): array
    {
        return [
            'data' => [
                'paymentTermsUpdate'=> [
                    'paymentTerms' => null,
                    'userErrors' => [
                        [
                            'code' => 'PAYMENT_TERMS_UPDATE_UNSUCCESSFUL',
                            'field' => null,
                            'message' => 'Cannot create payment terms on an Order that has already been paid in full.',
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function getShopifyUpdatePaymentTermsClientErrorResponse(): array
    {
        return [
            'data' => [
                'paymentTermsUpdate'=> [
                    'paymentTerms' => null,
                    'userErrors' => [
                        [
                            'code' => 'PAYMENT_TERMS_UPDATE_UNSUCCESSFUL',
                            'field' => null,
                            'message' => 'other error',
                        ],
                    ],
                ],
            ],
        ];
    }
}
