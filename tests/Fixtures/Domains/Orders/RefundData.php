<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Orders;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Ramsey\Uuid\Uuid;
use function random_int;

trait RefundData
{
    public static function refundDataProvider(): array
    {
        $store_id = Uuid::uuid4()->toString();
        $order_id = Uuid::uuid4()->toString();

        /** @var array<array{tbybItems: array<int>, upfrontItems: array<int>, discount: bool, taxes: bool, refundTbyb: array<int>, refundUpfront: array<int>, orderLevelAdjustments: bool, shopCurrency: string, presentmentCurrency: string> $data } */
        $data = [
            '1 tbyb (q: 1), refund: +order USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::ofMinor(1300, 'USD'),
                'order_level_refund_customer_amount' => Money::ofMinor(1300, 'USD'),
            ],
            '1 tbyb (q: 1), refund: +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::ofMinor(1600, 'CAD'),
                'order_level_refund_customer_amount' => Money::ofMinor(1300, 'USD'),
            ],
            '1 tbyb (q: 1), refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), refund: 1 tbyb (q: 2) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(20000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), refund: 1 tbyb (q: 4) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(40000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(40000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +discount, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, refund: 1 tbyb (q: 2) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(18000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, refund: 1 tbyb (q: 4) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(36000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(36000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), refund: 2 tbyb (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), refund: 2 tbyb (q: 1, 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(30000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(30000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), refund: 2 tbyb (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(50000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), refund: 2 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +discount, refund: 2 tbyb (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(45000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(45000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +discount, refund: 2 tbyb (q: 1, 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(27000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(27000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +discount, refund: 2 tbyb (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(45000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(45000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +discount, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +taxes, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 2) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 4) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(42600, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +discount, +taxes, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 2) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 4) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(38340, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(31950, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +taxes, refund: 1 tbyb (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +taxes, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(28755, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +taxes, +discount, refund: 2 tbyb (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +taxes, +discount, refund: 1 tbyb (q: 1) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +taxes, refund: 1 tbyb (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 2) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 4) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(42600, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +discount, +taxes, refund: 1 tbyb (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 2) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 4) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(38340, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, refund: 1 tbyb (q: 1, 2) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, refund: 1 tbyb (q: 1, 1) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(31950, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +taxes, refund: 1 tbyb (q: 1, 2) +order USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +taxes, refund: 1 tbyb (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 2) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 1) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(28755, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +taxes, +discount, refund: 2 tbyb (q: 1, 2) +order USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +taxes, +discount, refund: 1 tbyb (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), refund: 1 tbyb (q: 2), 1 upfront (q: 2) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(20000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(20000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(20000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), refund: 1 tbyb (q: 4), 1 upfront (q: 4) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(40000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(40000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(40000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(40000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +discount, refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, refund: 1 tbyb (q: 2), 1 upfront (q: 2) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(18000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(18000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(18000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, refund: 1 tbyb (q: 4), 1 upfront (q: 4) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(36000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(36000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(36000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(36000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(50000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(50000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(30000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(30000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(30000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(30000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(50000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(50000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), refund: 2 tbyb (q: 1), 2 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +discount, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(27000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(27000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(27000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(27000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(45000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(45000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(45000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(45000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +discount, refund: 2 tbyb (q: 1), 2 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 2), 1 upfront (q: 2) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 4), 1 upfront (q: 4) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(42600, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +discount, +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 2), 1 upfront (q: 2) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 4), 1 upfront (q: 4) USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(38340, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(31950, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +taxes, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +taxes, refund: 2 tbyb (q: 1), 2 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(28755, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +taxes, +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +taxes, +discount, refund: 1 tbyb (q: 1), 1 upfront (q: 1) USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 2), 1 upfront (q: 2) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 4), 1 upfront (q: 4) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(42600, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +discount, +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 2), 1 upfront (q: 2) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 4), 1 upfront (q: 4) +order USD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(38340, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(31950, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +taxes, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) +order USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) +order USD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(28755, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +taxes, +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) +order USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +taxes, +discount, refund: 2 tbyb (q: 1), 2 upfront (q: 1) +order USD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'USD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('USD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), refund: 1 tbyb (q: 2) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(40000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(20000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), refund: 1 tbyb (q: 4) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(80000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(40000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +discount, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, refund: 1 tbyb (q: 2) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(36000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(18000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, refund: 1 tbyb (q: 4) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(72000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(36000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), refund: 2 tbyb (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), refund: 2 tbyb (q: 1, 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(60000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(30000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), refund: 2 tbyb (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(100000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), refund: 2 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +discount, refund: 2 tbyb (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(90000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(45000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +discount, refund: 2 tbyb (q: 1, 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(54000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(27000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +discount, refund: 2 tbyb (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(90000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(45000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +discount, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +taxes, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 2) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(42600, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 4) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(85200, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +discount, +taxes, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 2) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(38340, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 4) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(76680, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(63900, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +taxes, refund: 1 tbyb (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +taxes, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(57510, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +taxes, +discount, refund: 2 tbyb (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +taxes, +discount, refund: 1 tbyb (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +taxes, refund: 1 tbyb (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 2) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(42600, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +taxes, refund: 1 tbyb (q: 4) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(85200, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), +discount, +taxes, refund: 1 tbyb (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 2) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(38340, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), +discount, +taxes, refund: 1 tbyb (q: 4) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(76680, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, refund: 1 tbyb (q: 1, 2) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, refund: 1 tbyb (q: 1, 1) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(63900, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +taxes, refund: 1 tbyb (q: 1, 2) +order CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +taxes, refund: 1 tbyb (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 2) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 1) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(57510, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), +taxes, +discount, refund: 2 tbyb (q: 1, 2) +order CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), +taxes, +discount, refund: 1 tbyb (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(0, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(0, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), refund: 1 tbyb (q: 2), 1 upfront (q: 2) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(40000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(20000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(40000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(20000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), refund: 1 tbyb (q: 4), 1 upfront (q: 4) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(80000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(40000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(80000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(40000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +discount, refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, refund: 1 tbyb (q: 2), 1 upfront (q: 2) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(36000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(18000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(36000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(18000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, refund: 1 tbyb (q: 4), 1 upfront (q: 4) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(72000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(36000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(72000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(36000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(100000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(100000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(60000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(30000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(60000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(30000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(100000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(100000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(50000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), refund: 2 tbyb (q: 1), 2 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => false,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(20000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +discount, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(54000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(27000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(54000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(27000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(90000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(45000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(90000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(45000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +discount, refund: 2 tbyb (q: 1), 2 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => true,
                'taxes' => false,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(18000, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9000, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 2), 1 upfront (q: 2) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(42600, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(42600, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 4), 1 upfront (q: 4) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(85200, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(85200, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +discount, +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 2), 1 upfront (q: 2) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(38340, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(38340, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 4), 1 upfront (q: 4) CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(76680, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(76680, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(63900, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(63900, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +taxes, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +taxes, refund: 2 tbyb (q: 1), 2 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(57510, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(57510, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +taxes, +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +taxes, +discount, refund: 1 tbyb (q: 1), 1 upfront (q: 1) CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 2), 1 upfront (q: 2) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => false,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(42600, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(42600, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(21300, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +taxes, refund: 1 tbyb (q: 4), 1 upfront (q: 4) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(85200, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(85200, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(42600, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 1), 1 upfront (q: 1), +discount, +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                ],
                'upfrontItems' => [
                    0 => 1,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 2), 1 upfront (q: 2) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 2,
                ],
                'refundUpfront' => [
                    0 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(38340, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(38340, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(19170, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '1 tbyb (q: 4), 1 upfront (q: 4), +discount, +taxes, refund: 1 tbyb (q: 4), 1 upfront (q: 4) +order CAD' => [
                'tbybItems' => [
                    0 => 4,
                ],
                'upfrontItems' => [
                    0 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 4,
                ],
                'refundUpfront' => [
                    0 => 4,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(76680, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(76680, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(38340, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(63900, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(63900, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(31950, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +taxes, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) +order CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(106500, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(53250, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +taxes, refund: 1 tbyb (q: 1), 1 upfront (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => false,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(21300, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(10650, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 1, 2), 2 upfront (q: 1, 2), +taxes, +discount, refund: 2 tbyb (q: 1, 1), 2 upfront (q: 1, 1) +order CAD' => [
                'tbybItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'upfrontItems' => [
                    0 => 1,
                    1 => 2,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(57510, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(57510, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(28755, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 4), 2 upfront (q: 2, 4), +taxes, +discount, refund: 2 tbyb (q: 1, 2), 2 upfront (q: 1, 2) +order CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 4,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                    1 => 2,
                ],
                'refundUpfront' => [
                    0 => 1,
                    1 => 2,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(95850, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(47925, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
            '2 tbyb (q: 2, 1), 2 upfront (q: 2, 1), +taxes, +discount, refund: 2 tbyb (q: 1), 2 upfront (q: 1) +order CAD' => [
                'tbybItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'upfrontItems' => [
                    0 => 2,
                    1 => 1,
                ],
                'discount' => true,
                'taxes' => true,
                'refundTbyb' => [
                    0 => 1,
                ],
                'refundUpfront' => [
                    0 => 1,
                ],
                'orderLevelAdjustments' => true,
                'shopCurrency' => 'CAD',
                'presentmentCurrency' => 'USD',
                'tbyb_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'tbyb_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'upfront_total_shop_amount' => Money::ofMinor(19170, 'CAD'),
                'upfront_total_customer_amount' => Money::ofMinor(9585, 'USD'),
                'order_level_refund_shop_amount' => Money::zero('CAD'),
                'order_level_refund_customer_amount' => Money::zero('USD'),
            ],
        ];

        return self::generateTestData($data, $order_id, $store_id);
    }

    protected static function generateTestData(array $data, string $order_id, string $store_id): array
    {
        $refund_id = Uuid::uuid4()->toString();

        $amounts = [
            'USD' => [
                'lineItem' => Money::ofMinor(10000, 'USD'),
                'discount' => Money::ofMinor((int) (10000 * 0.1), 'USD'),
                'taxes' => 0.065,
                'orderLevelAdjustments' => Money::ofMinor(1300, 'USD'),
                'deposit' => Money::ofMinor((int) (10000 * 0.1), 'USD'),
            ],
            'CAD' => [
                'lineItem' => Money::ofMinor(20000, 'CAD'),
                'discount' => Money::ofMinor((int) (20000 * 0.1), 'CAD'),
                'taxes' => 0.065,
                'orderLevelAdjustments' => Money::ofMinor(1600, 'CAD'),
                'deposit' => Money::ofMinor((int) (20000 * 0.1), 'CAD'),
            ],
        ];

        $tests = [];

        foreach ($data as $testName => $testCase) {
            $lineItems = [];
            foreach ($testCase['tbybItems'] as $i => $quantity) {
                $lineItems[] = self::getLineItem($order_id, $testCase, $amounts, $quantity, true, $i);
            }

            foreach ($testCase['upfrontItems'] as $i => $quantity) {
                $lineItems[] = self::getLineItem($order_id, $testCase, $amounts, $quantity, false, $i);
            }

            $refundLineItems = [];
            foreach ($testCase['refundTbyb'] as $key => $quantity) {
                $refundLineItems[] = self::getRefundLineItem(
                    $lineItems[$key],
                    $testCase,
                    $amounts,
                    $quantity,
                    true,
                    $key,
                    $refund_id
                );
            }

            foreach ($testCase['refundUpfront'] as $key => $quantity) {
                $refundLineItems[] = self::getRefundLineItem(
                    $lineItems[$key],
                    $testCase,
                    $amounts,
                    $quantity,
                    false,
                    $key,
                    $refund_id
                );
            }

            // dd($lineItems, $refundLineItems);

            $refund = self::getRefund($order_id, $store_id, $testCase, $amounts, $refundLineItems, $refund_id);

            $webhookData = [
                'id' => '24680',
                'refund_line_items' => [],
                'order_adjustments' => [],
            ];

            foreach ($refundLineItems as $key => $refundLineItem) {
                $key += 1;
                $webhookData['refund_line_items'][] = [
                    'id' => '12345678-1234-1234-1234-123456789012',
                    'line_item_id' => 'source_' . $refundLineItem['line_item_id'],
                    'quantity' => $refundLineItem['quantity'],
                    'restock_type' => 'no_restock',
                    'subtotal_set' => [
                        'shop_money' => [
                            'amount' => $refundLineItem['gross_sales_shop_amount']->minus($testCase['discount'] ? $refundLineItem['discounts_shop_amount'] : 0)->getAmount()->toFloat(),
                            'currency_code' => $testCase['shopCurrency'],
                        ],
                        'presentment_money' => [
                            'amount' => $refundLineItem['gross_sales_customer_amount']->minus($testCase['discount'] ? $refundLineItem['discounts_customer_amount'] : 0)->getAmount()->toFloat(),
                            'currency_code' => $testCase['presentmentCurrency'],
                        ],
                    ],
                    'total_tax_set' => [
                        'shop_money' => [
                            'amount' => $testCase['taxes'] ? $refundLineItem['tax_shop_amount']->getAmount()->toFloat() : 0,
                            // dollarst
                            'currency_code' => $testCase['shopCurrency'],
                        ],
                        'presentment_money' => [
                            'amount' => $testCase['taxes'] ? $refundLineItem['tax_customer_amount']->getAmount()->toFloat() : 0,
                            'currency_code' => $testCase['presentmentCurrency'],
                        ],
                    ],
                    'line_item' => [
                        'id' => 'source_98765-' . (((bool) $refundLineItem['is_tbyb']) ? 'tbyb' : 'upfront') . '-' . $key,
                        'variant_id' => 'source_12345-' . $key,
                        'title' => 'Test Product ' . $key,
                        'quantity' => $refundLineItem['quantity'],
                        'sku' => 'test-sku-' . $key,
                        'variant_title' => 'Test Variant ' . $key,
                        'vendor' => 'Test Vendor',
                        'fulfillment_service' => 'shopify',
                        'product_id' => 'source_12345-' . $key,
                        'requires_shipping' => true,
                        'taxable' => true,
                        'gift_card' => false,
                        'name' => 'Test Product',
                        'variant_inventory_management' => 'shopify',
                        'properties' => [],
                        'product_exists' => true,
                        'fulfillable_quantity' => $refundLineItem['quantity'],
                        'grams' => 100,
                        'price' => 'ignored',
                        'total_discount' => 'ignored',
                        'fulfillment_status' => 'fulfilled',
                        'price_set' => [
                            'shop_money' => [
                                'amount' => $refundLineItem['gross_sales_shop_amount']->dividedBy(
                                    $refundLineItem['quantity'],
                                    RoundingMode::HALF_EVEN
                                )->getAmount()->toFloat(),
                                'currency_code' => $testCase['shopCurrency'],
                            ],
                            'presentment_money' => [
                                'amount' => $refundLineItem['gross_sales_customer_amount']->dividedBy(
                                    $refundLineItem['quantity'],
                                    RoundingMode::HALF_EVEN
                                )->getAmount()->toFloat(),
                                'currency_code' => $testCase['presentmentCurrency'],
                            ],
                        ],
                        'total_discount_set' => [
                            'shop_money' => [
                                'amount' => 0, // always 0
                                'currency_code' => 'USD',
                            ],
                            'presentment_money' => [
                                'amount' => 0, // always 0
                                'currency_code' => 'USD',
                            ],
                        ],
                        'discount_allocations' => [], // ignored
                        'tax_lines' => [], // ignored
                    ],
                ];
            }

            if (count($testCase['refundTbyb']) + count($testCase['refundUpfront']) > 0) {
                $webhookData['order_adjustments'][] = [
                    'order_id' => $order_id,
                    'amount_set' => [
                        'shop_money' => [
                            'amount' => $refund['tbyb_total_shop_amount']->plus($refund['upfront_total_shop_amount'])->plus($refund['tbyb_deposit_shop_amount'])->multipliedBy(
                                -1,
                                RoundingMode::HALF_EVEN
                            )->getAmount()->toFloat(),
                            'currency_code' => $testCase['shopCurrency'],
                        ],
                        'presentment_money' => [
                            'amount' => $refund['tbyb_total_customer_amount']->plus($refund['upfront_total_customer_amount'])->plus($refund['tbyb_deposit_customer_amount'])->multipliedBy(
                                -1,
                                RoundingMode::HALF_EVEN
                            )->getAmount()->toFloat(),
                            'currency_code' => $testCase['presentmentCurrency'],
                        ],
                    ],
                ];

                $webhookData['order_adjustments'][] = [
                    'order_id' => $order_id,
                    'amount_set' => [
                        'shop_money' => [
                            'amount' => $refund['tbyb_total_shop_amount']->plus($refund['upfront_total_shop_amount'])->plus($refund['tbyb_deposit_shop_amount'])->getAmount()->toFloat(),
                            'currency_code' => $testCase['shopCurrency'],
                        ],
                        'presentment_money' => [
                            'amount' => $refund['tbyb_total_customer_amount']->plus($refund['upfront_total_customer_amount'])->plus($refund['tbyb_deposit_customer_amount'])->getAmount()->toFloat(),
                            'currency_code' => $testCase['presentmentCurrency'],
                        ],
                    ],
                ];
            }

            if ($testCase['orderLevelAdjustments']) {
                $webhookData['order_adjustments'][] = [
                    'order_id' => $order_id,
                    'amount_set' => [
                        'shop_money' => [
                            'amount' => isset($testCase['order_level_refund_shop_amount']) ? $testCase['order_level_refund_shop_amount']->getAmount()->toFloat() : $refund['order_level_refund_shop_amount']->getAmount()->toFloat(),
                            'currency_code' => $testCase['shopCurrency'],
                        ],
                        'presentment_money' => [
                            'amount' => isset($testCase['order_level_refund_customer_amount']) ? $testCase['order_level_refund_customer_amount']->getAmount()->toFloat() : $refund['order_level_refund_customer_amount']->getAmount()->toFloat(),
                            'currency_code' => $testCase['presentmentCurrency'],
                        ],
                    ],
                ];
            }

            foreach ($refundLineItems as $key => $refundLineItem) {
                $refundLineItems[$key]['deposit_customer_amount'] = $refundLineItem['deposit_customer_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['deposit_shop_amount'] = $refundLineItem['deposit_shop_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['discounts_customer_amount'] = $refundLineItem['discounts_customer_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['discounts_shop_amount'] = $refundLineItem['discounts_shop_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['gross_sales_customer_amount'] = $refundLineItem['gross_sales_customer_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['gross_sales_shop_amount'] = $refundLineItem['gross_sales_shop_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['tax_customer_amount'] = $refundLineItem['tax_customer_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['tax_shop_amount'] = $refundLineItem['tax_shop_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['total_customer_amount'] = $refundLineItem['total_customer_amount']->getMinorAmount()->toInt();
                $refundLineItems[$key]['total_shop_amount'] = $refundLineItem['total_shop_amount']->getMinorAmount()->toInt();
            }

            $refund['tbyb_deposit_customer_amount'] = $refund['tbyb_deposit_customer_amount']->getMinorAmount()->toInt();
            $refund['tbyb_deposit_shop_amount'] = $refund['tbyb_deposit_shop_amount']->getMinorAmount()->toInt();
            $refund['tbyb_discounts_customer_amount'] = $refund['tbyb_discounts_customer_amount']->getMinorAmount()->toInt();
            $refund['tbyb_discounts_shop_amount'] = $refund['tbyb_discounts_shop_amount']->getMinorAmount()->toInt();
            $refund['tbyb_gross_sales_customer_amount'] = $refund['tbyb_gross_sales_customer_amount']->getMinorAmount()->toInt();
            $refund['tbyb_gross_sales_shop_amount'] = $refund['tbyb_gross_sales_shop_amount']->getMinorAmount()->toInt();
            $refund['upfront_discounts_customer_amount'] = $refund['upfront_discounts_customer_amount']->getMinorAmount()->toInt();
            $refund['upfront_discounts_shop_amount'] = $refund['upfront_discounts_shop_amount']->getMinorAmount()->toInt();
            $refund['upfront_gross_sales_customer_amount'] = $refund['upfront_gross_sales_customer_amount']->getMinorAmount()->toInt();
            $refund['upfront_gross_sales_shop_amount'] = $refund['upfront_gross_sales_shop_amount']->getMinorAmount()->toInt();

            if (!isset($refund['tbyb_total_shop_amount'])) {
                $refund['tbyb_total_customer_amount'] = $refund['tbyb_total_customer_amount']->getMinorAmount()->toInt();
                $refund['tbyb_total_shop_amount'] = $refund['tbyb_total_shop_amount']->getMinorAmount()->toInt();
                $refund['upfront_total_customer_amount'] = $refund['upfront_total_customer_amount']->getMinorAmount()->toInt();
                $refund['upfront_total_shop_amount'] = $refund['upfront_total_shop_amount']->getMinorAmount()->toInt();
            }

            if (count($webhookData['order_adjustments']) > 0) {
                $lastAdjustment = last($webhookData['order_adjustments']);
                $refund['refunded_shop_amount'] = Money::of(
                    $lastAdjustment['amount_set']['shop_money']['amount'],
                    $lastAdjustment['amount_set']['shop_money']['currency_code'],
                );
                $refund['refunded_customer_amount'] = Money::of(
                    $lastAdjustment['amount_set']['presentment_money']['amount'],
                    $lastAdjustment['amount_set']['presentment_money']['currency_code'],
                );
            }

            $tests[$testName] = [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => $testCase['shopCurrency'],
                'customerCurrency' => $testCase['presentmentCurrency'],
                'lineItems' => $lineItems,
                'webhookData' => $webhookData,
                'refundLineItems' => $refundLineItems,
                'refund' => $refund,
            ];
        }

        // dd($tests[$testName]);

        return $tests;
    }

    /**
     * @param array{USD: array<Money>, CAD: array<Money>} $amounts
     */
    protected static function getLineItem(
        string $order_id,
        array $testCase,
        array $amounts,
        int $quantity,
        bool $isTbyb,
        int $multiplier
    ): array {
        $multiplier += 1;

        return [
            'id' => '98765-' . ($isTbyb ? 'tbyb' : 'upfront') . '-' . $multiplier,
            'order_id' => $order_id,
            'quantity' => $quantity,
            'source_id' => 'source_98765-' . ($isTbyb ? 'tbyb' : 'upfront') . '-' . $multiplier,
            'is_tbyb' => $isTbyb,
            'shop_currency' => $testCase['shopCurrency'],
            'customer_currency' => $testCase['presentmentCurrency'],
            'price_shop_amount' => $amounts[$testCase['shopCurrency']]['lineItem']->multipliedBy(
                $multiplier,
                RoundingMode::HALF_EVEN
            ),
            'price_customer_amount' => $amounts[$testCase['presentmentCurrency']]['lineItem']->multipliedBy(
                $multiplier,
                RoundingMode::HALF_EVEN
            ),
            'discount_shop_amount' => $testCase['discount'] ? $amounts[$testCase['shopCurrency']]['discount']->multipliedBy(
                $quantity * $multiplier,
                RoundingMode::HALF_EVEN
            ) : Money::zero($testCase['shopCurrency']),
            'discount_customer_amount' => $testCase['discount'] ? $amounts[$testCase['presentmentCurrency']]['discount']->multipliedBy(
                $quantity * $multiplier,
                RoundingMode::HALF_EVEN
            ) : Money::zero($testCase['presentmentCurrency']),
            'deposit_shop_amount' => $isTbyb ? $amounts[$testCase['shopCurrency']]['deposit']->multipliedBy(
                $quantity * $multiplier,
                RoundingMode::HALF_EVEN
            ) : Money::zero($testCase['shopCurrency']),
            'deposit_customer_amount' => $isTbyb ? $amounts[$testCase['presentmentCurrency']]['deposit']->multipliedBy(
                $quantity * $multiplier,
                RoundingMode::HALF_EVEN
            ) : Money::zero($testCase['presentmentCurrency']),
            'total_price_shop_amount' => $amounts[$testCase['shopCurrency']]['lineItem']->multipliedBy(
                $quantity * $multiplier,
                RoundingMode::HALF_EVEN
            )->minus($testCase['discount'] ? $amounts[$testCase['shopCurrency']]['discount'] : 0),
            'total_price_customer_amount' => $amounts[$testCase['presentmentCurrency']]['lineItem']->multipliedBy(
                $quantity * $multiplier,
                RoundingMode::HALF_EVEN
            )->minus($testCase['discount'] ? $amounts[$testCase['presentmentCurrency']]['discount'] : 0),
        ];
    }

    /**
     * @param array{id: string, order_id: string, quantity: int, source_id: string, is_tbyb: bool, shop_currency: string, customer_currency: string, price_shop_amount: Money, price_customer_amount: Money, discount_shop_amount: Money, discount_customer_amount: Money, deposit_shop_amount: Money, deposit_customer_amount: Money, total_price_shop_amount: Money, total_price_customer_amount: Money} $lineItem
     * @param array{USD: array<Money>, CAD: array<Money>} $amounts
     * @return array
     */
    protected static function getRefundLineItem(
        array $lineItem,
        array $testCase,
        array $amounts,
        int $quantity,
        bool $isTbyb,
        int $multiplier,
        string $refund_id
    ) {
        $multiplier += 1;

        $shopSubtotal = $lineItem['price_shop_amount']->multipliedBy($quantity)
            ->minus(($testCase['discount'] ? ($amounts[$testCase['shopCurrency']]['discount']->multipliedBy($quantity * $multiplier)) : 0));
        $customerSubtotal = $lineItem['price_customer_amount']->multipliedBy($quantity)
            ->minus(($testCase['discount'] ? ($amounts[$testCase['presentmentCurrency']]['discount']->multipliedBy($quantity * $multiplier)) : 0));

        $shopTaxes = $testCase['taxes']
            ? $shopSubtotal->multipliedBy($amounts[$testCase['shopCurrency']]['taxes'])
            : Money::zero($testCase['shopCurrency']);
        $customerTaxes = $testCase['taxes']
            ? $customerSubtotal->multipliedBy($amounts[$testCase['presentmentCurrency']]['taxes'])
            : Money::zero($testCase['presentmentCurrency']);

        return [
            'refund_id' => $refund_id,
            'line_item_id' => '98765-' . ($isTbyb ? 'tbyb' : 'upfront') . '-' . $multiplier,
            'quantity' => $quantity,
            'shop_currency' => $testCase['shopCurrency'],
            'customer_currency' => $testCase['presentmentCurrency'],
            'gross_sales_shop_amount' => $lineItem['price_shop_amount']->multipliedBy($quantity),
            'gross_sales_customer_amount' => $lineItem['price_customer_amount']->multipliedBy($quantity),
            'deposit_shop_amount' => $isTbyb ? $amounts[$testCase['shopCurrency']]['deposit']->multipliedBy(
                $quantity * $multiplier,
                RoundingMode::HALF_EVEN
            ) : Money::zero($testCase['shopCurrency']),
            'deposit_customer_amount' => $isTbyb ? $amounts[$testCase['presentmentCurrency']]['deposit']->multipliedBy(
                $quantity * $multiplier,
                RoundingMode::HALF_EVEN
            ) : Money::zero($testCase['presentmentCurrency']),
            'discounts_shop_amount' => $testCase['discount'] ? ($amounts[$testCase['shopCurrency']]['discount']->multipliedBy($quantity * $multiplier)) : Money::zero($testCase['shopCurrency']),
            'discounts_customer_amount' => $testCase['discount'] ? ($amounts[$testCase['presentmentCurrency']]['discount']->multipliedBy($quantity * $multiplier)) : Money::zero($testCase['presentmentCurrency']),
            'is_tbyb' => $isTbyb ? 1 : 0,
            'tax_shop_amount' => $shopTaxes,
            'tax_customer_amount' => $customerTaxes,
            'total_shop_amount' => $shopSubtotal->plus($shopTaxes),
            'total_customer_amount' => $customerSubtotal->plus($customerTaxes),
        ];
    }

    /**
     * @param array{USD: array<Money>, CAD: array<Money>} $amounts
     */
    protected static function getRefund(
        string $order_id,
        string $store_id,
        array $testCase,
        array $amounts,
        array $refundLineItems,
        string $refund_id
    ): array {
        $refund = [
            'id' => $refund_id,
            'source_refund_reference_id' => 'gid://shopify/Refund/24680',
            'order_id' => $order_id,
            'store_id' => $store_id,
            'shop_currency' => $testCase['shopCurrency'],
            'customer_currency' => $testCase['presentmentCurrency'],
            'order_level_refund_customer_amount' => empty($refundLineItems) && $testCase['orderLevelAdjustments'] ? $amounts[$testCase['presentmentCurrency']]['orderLevelAdjustments'] : Money::zero($testCase['presentmentCurrency']),
            'order_level_refund_shop_amount' => empty($refundLineItems) && $testCase['orderLevelAdjustments'] ? $amounts[$testCase['shopCurrency']]['orderLevelAdjustments'] : Money::zero($testCase['shopCurrency']),
            'tbyb_deposit_customer_amount' => Money::zero($testCase['presentmentCurrency']),
            'tbyb_deposit_shop_amount' => Money::zero($testCase['shopCurrency']),
            'tbyb_discounts_customer_amount' => Money::zero($testCase['presentmentCurrency']),
            'tbyb_discounts_shop_amount' => Money::zero($testCase['shopCurrency']),
            'tbyb_gross_sales_customer_amount' => Money::zero($testCase['presentmentCurrency']),
            'tbyb_gross_sales_shop_amount' => Money::zero($testCase['shopCurrency']),
            'tbyb_total_customer_amount' => Money::zero($testCase['presentmentCurrency']),
            'tbyb_total_shop_amount' => Money::zero($testCase['shopCurrency']),
            'upfront_discounts_customer_amount' => Money::zero($testCase['presentmentCurrency']),
            'upfront_discounts_shop_amount' => Money::zero($testCase['shopCurrency']),
            'upfront_gross_sales_customer_amount' => Money::zero($testCase['presentmentCurrency']),
            'upfront_gross_sales_shop_amount' => Money::zero($testCase['shopCurrency']),
            'upfront_total_customer_amount' => Money::zero($testCase['presentmentCurrency']),
            'upfront_total_shop_amount' => Money::zero($testCase['shopCurrency']),
            'refunded_customer_amount' => Money::zero($testCase['presentmentCurrency']),
            'refunded_shop_amount' => Money::zero($testCase['shopCurrency']),
        ];

        if (isset($testCase['upfront_total_customer_amount'])) {
            $refund['upfront_total_customer_amount'] = $testCase['upfront_total_customer_amount'];
            $refund['upfront_total_shop_amount'] = $testCase['upfront_total_shop_amount'];
            $refund['tbyb_total_customer_amount'] = $testCase['tbyb_total_customer_amount'];
            $refund['tbyb_total_shop_amount'] = $testCase['tbyb_total_shop_amount'];
            $refund['order_level_refund_customer_amount'] = $testCase['order_level_refund_customer_amount'];
            $refund['order_level_refund_shop_amount'] = $testCase['order_level_refund_shop_amount'];
        }

        foreach ($refundLineItems as $refundLineItem) {
            if ($refundLineItem['is_tbyb']) {
                $refund['tbyb_deposit_customer_amount'] = $refund['tbyb_deposit_customer_amount']->plus(
                    $refundLineItem['deposit_customer_amount'],
                    RoundingMode::HALF_EVEN
                );
                $refund['tbyb_deposit_shop_amount'] = $refund['tbyb_deposit_shop_amount']->plus(
                    $refundLineItem['deposit_shop_amount'],
                    RoundingMode::HALF_EVEN
                );
                $refund['tbyb_discounts_customer_amount'] = $refund['tbyb_discounts_customer_amount']->plus(
                    $refundLineItem['discounts_customer_amount'],
                    RoundingMode::HALF_EVEN
                );
                $refund['tbyb_discounts_shop_amount'] = $refund['tbyb_discounts_shop_amount']->plus(
                    $refundLineItem['discounts_shop_amount'],
                    RoundingMode::HALF_EVEN
                );
                $refund['tbyb_gross_sales_customer_amount'] = $refund['tbyb_gross_sales_customer_amount']->plus(
                    $refundLineItem['gross_sales_customer_amount'],
                    RoundingMode::HALF_EVEN
                );
                $refund['tbyb_gross_sales_shop_amount'] = $refund['tbyb_gross_sales_shop_amount']->plus(
                    $refundLineItem['gross_sales_shop_amount'],
                    RoundingMode::HALF_EVEN
                );

                if (!isset($testCase['tbyb_total_shop_amount'])) {
                    $refund['tbyb_total_shop_amount'] = $refund['tbyb_total_shop_amount']->plus(
                        $refundLineItem['total_shop_amount'],
                        RoundingMode::HALF_EVEN
                    );
                    $refund['tbyb_total_customer_amount'] = $refund['tbyb_total_customer_amount']->plus(
                        $refundLineItem['total_customer_amount'],
                        RoundingMode::HALF_EVEN
                    );
                }

                // $refund['order_level_refund_customer_amount'] = $refund['order_level_refund_customer_amount']->plus($refund['tbyb_deposit_customer_amount']);
                // $refund['order_level_refund_shop_amount'] = $refund['order_level_refund_shop_amount']->plus($refund['tbyb_deposit_shop_amount']);
            }

            if (!$refundLineItem['is_tbyb']) {
                $refund['upfront_gross_sales_shop_amount'] = $refund['upfront_gross_sales_shop_amount']->plus($refundLineItem['gross_sales_shop_amount']);
                $refund['upfront_gross_sales_customer_amount'] = $refund['upfront_gross_sales_customer_amount']->plus($refundLineItem['gross_sales_customer_amount']);
                $refund['upfront_discounts_shop_amount'] = $refund['upfront_discounts_shop_amount']->plus($refundLineItem['discounts_shop_amount']);
                $refund['upfront_discounts_customer_amount'] = $refund['upfront_discounts_customer_amount']->plus($refundLineItem['discounts_customer_amount']);

                if (!isset($testCase['upfront_total_shop_amount'])) {
                    $refund['upfront_total_shop_amount'] = $refund['upfront_total_shop_amount']->plus($refundLineItem['total_shop_amount']);
                    $refund['upfront_total_customer_amount'] = $refund['upfront_total_customer_amount']->plus($refundLineItem['total_customer_amount']);
                }
            }
        }

        return $refund;
    }

    public static function generatedDataProvider(): array
    {
        $store_id = Uuid::uuid4()->toString();
        $order_id = Uuid::uuid4()->toString();

        /** @var array<array{tbybItems: array<int>, upfrontItems: array<int>, discount: bool, taxes: bool, refundTbyb: array<int>, refundUpfront: array<int>, orderLevelAdjustments: bool, shopCurrency: string, presentmentCurrency: string> $data } */
        $data = self::generateTestCases(50);

        return self::generateTestData($data, $order_id, $store_id);
    }

    public static function generateTestCases($totalCases)
    {
        static $testCases = [];

        if (count($testCases) > 0) {
            return $testCases;
        }

        // Generate test cases evenly distributed across each facet
        for ($i = 0; $i < $totalCases; $i++) {
            $tbybItems = self::generateItems(1, 3);
            $upfrontItems = random_int(0, 1000) % 2 ? self::generateItems(0, 3) : [];
            ['refundTbyb' => $refundTbyb, 'refundUpfront' => $refundUpfront] = self::generateRefundItems(
                $tbybItems,
                $upfrontItems
            );
            $discount = (bool) (random_int(0, 1000) % 2);
            $taxes = (bool) (random_int(0, 1000) % 2);
            $orderLevelAdjustments = (bool) (random_int(0, 1000) % 2);
            $shopCurrency = (bool) (random_int(0, 1000) % 2) ? 'USD' : 'CAD';

            $testName = self::generateTestName(
                $tbybItems,
                $upfrontItems,
                $refundTbyb,
                $refundUpfront,
                $discount,
                $taxes,
                $shopCurrency,
                $orderLevelAdjustments
            );

            $testCases[$testName] = [
                'tbybItems' => $tbybItems,
                'upfrontItems' => $upfrontItems,
                'discount' => $discount,
                'taxes' => $taxes,
                'refundTbyb' => $refundTbyb,
                'refundUpfront' => $refundUpfront,
                'orderLevelAdjustments' => $orderLevelAdjustments,
                'shopCurrency' => $shopCurrency,
                'presentmentCurrency' => 'USD',
            ];
        }

        ksort($testCases);

        return $testCases;
    }

    protected static function generateItems($minimumItems)
    {
        $items = array_unique([$minimumItems, 1, 2, 4]);
        $count = $items[array_rand($items)];
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $items[] = [1, 2, 4][random_int(0, 2)];
        }

        return $items;
    }

    protected static function generateRefundItems($tbybItems, $upfrontItems)
    {
        $refundItems = [];

        $refundTbyb = true;
        $refundUpfront = false;

        if (!empty($upfrontItems)) {
            $refundTbyb = (bool) (random_int(0, 1000) % 2);
            if ($refundTbyb) {
                $refundUpfront = (bool) (random_int(0, 1000) % 2);
            }

            if (!$refundTbyb) {
                $refundUpfront = true;
            }
        }

        $refundItems['refundTbyb'] = [];
        $refundItems['refundUpfront'] = [];

        if ($refundTbyb) {
            $q = match (count($tbybItems)) {
                1 => [1],
                2 => [1, 2],
                4 => [1, 2, 4],
            };
            $refundableItems = array_rand($tbybItems, $q[array_rand($q)]);
            if (is_int($refundableItems)) {
                $refundableItems = [$refundableItems];
            }
            foreach ($refundableItems as $item) {
                $q = match ($tbybItems[$item]) {
                    1 => [1],
                    2 => [1, 2],
                    4 => [1, 2, 4],
                };
                $refundItems['refundTbyb'][] = $q[array_rand($q)];
            }
        }

        if ($refundUpfront) {
            $q = match (count($upfrontItems)) {
                1 => [1],
                2 => [1, 2],
                4 => [1, 2, 4],
            };
            $refundableItems = array_rand($upfrontItems, $q[array_rand($q)]);
            if (is_int($refundableItems)) {
                $refundableItems = [$refundableItems];
            }
            foreach ($refundableItems as $item) {
                $q = match ($upfrontItems[$item]) {
                    1 => [1],
                    2 => [1, 2],
                    4 => [1, 2, 4],
                };
                $refundItems['refundUpfront'][] = $q[array_rand($q)];
            }
        }

        return $refundItems;
    }

    protected static function generateTestName(
        $tbybItems,
        $upfrontItems,
        $refundTbyb,
        $refundUpfront,
        $discount,
        $taxes,
        $currency,
        $orderLevelAdjustments
    ) {
        $testName = '[generated] ';
        // Append TBYB items
        $testName .= count($tbybItems) . ' tbyb (q: ' . implode(', ', $tbybItems) . ')';
        // Append upfront items if any
        if (!empty($upfrontItems)) {
            $testName .= ', ' . count($upfrontItems) . ' upfront (q: ' . implode(', ', $upfrontItems) . ')';
        }
        // Append discount if applied
        if ($discount) {
            $testName .= ' +discount';
        }
        // Append taxes if applied
        if ($taxes) {
            $testName .= ' +taxes';
        }

        $testName .= ', refund:';

        // Append refundTbyb items if any
        if (!empty($refundTbyb)) {
            $testName .= ' ' . count($refundTbyb) . ' tbyb (q: ' . implode(', ', $refundTbyb) . ')';
        }
        // Append refundUpfront items if any
        if (!empty($refundUpfront)) {
            $testName .= ' ' . count($refundUpfront) . ' upfront (q: ' . implode(', ', $refundUpfront) . ')';
        }

        if ($orderLevelAdjustments) {
            $testName .= ' +order';
        }

        // Append shop currency
        $testName .= ' ' . $currency;

        return $testName;
    }

    public static function realWebhookDataProvider(): array
    {
        $store_id = Uuid::uuid4()->toString();
        $order_id = Uuid::uuid4()->toString();
        $refund_id = Uuid::uuid4()->toString();

        return [
            '[real] 1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: 1 tbyb +additional ($10)' => [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => 'CAD',
                'customerCurrency' => 'USD',
                'lineItems' => [
                    [
                        'id' => 10956880806017,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956880806017',
                        'is_tbyb' => true,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 55144,
                        'price_customer_amount' => 40900,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 5514,
                        'deposit_customer_amount' => 4090,
                    ],
                    [
                        'id' => 10956907446401,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956907446401',
                        'is_tbyb' => false,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 68896,
                        'price_customer_amount' => 51100,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 0,
                        'deposit_customer_amount' => 0,
                    ],
                ],
                'webhookData' => json_decode(
                    '{"id":829178642561,"order_id":4181876637825,"created_at":"2024-03-08T13:18:00-05:00","note":"","user_id":73836855425,"processed_at":"2024-03-08T13:18:00-05:00","restock":true,"duties":[],"total_duties_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"additional_fees":[],"total_additional_fees_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"return":null,"refund_shipping_lines":[],"admin_graphql_api_id":"gid:\/\/shopify\/Refund\/829178642561","refund_line_items":[{"id":297508864129,"quantity":1,"line_item_id":10956880806017,"location_id":63239716993,"restock_type":"cancel","subtotal":551.44,"total_tax":55.7,"subtotal_set":{"shop_money":{"amount":"551.44","currency_code":"CAD"},"presentment_money":{"amount":"409.00","currency_code":"USD"}},"total_tax_set":{"shop_money":{"amount":"55.70","currency_code":"CAD"},"presentment_money":{"amount":"41.31","currency_code":"USD"}},"line_item":{"id":10956880806017,"variant_id":39732920746113,"title":"The Collection Snowboard: Hydrogen","quantity":1,"sku":"","variant_title":null,"vendor":"Hydrogen Vendor","fulfillment_service":"manual","product_id":6672585228417,"requires_shipping":true,"taxable":true,"gift_card":false,"name":"The Collection Snowboard: Hydrogen","variant_inventory_management":"shopify","properties":[],"product_exists":true,"fulfillable_quantity":0,"grams":0,"price":"551.44","total_discount":"0.00","fulfillment_status":null,"price_set":{"shop_money":{"amount":"551.44","currency_code":"CAD"},"presentment_money":{"amount":"409.00","currency_code":"USD"}},"total_discount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"discount_allocations":[],"duties":[],"admin_graphql_api_id":"gid:\/\/shopify\/LineItem\/10956880806017","tax_lines":[{"title":"Washington State Tax","price":"35.85","rate":0.065,"channel_liable":false,"price_set":{"shop_money":{"amount":"35.85","currency_code":"CAD"},"presentment_money":{"amount":"26.59","currency_code":"USD"}}},{"title":"Pierce County Tax","price":"0.00","rate":0.0,"channel_liable":false,"price_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"title":"Puyallup City Tax","price":"19.85","rate":0.036,"channel_liable":false,"price_set":{"shop_money":{"amount":"19.85","currency_code":"CAD"},"presentment_money":{"amount":"14.72","currency_code":"USD"}}}]}}],"transactions":[{"id":5489326751873,"order_id":4181876637825,"kind":"refund","gateway":"shopify_payments","status":"pending","message":"Refund submitted and processing. Learn more about \u003ca href=\"https:\/\/help.shopify.com\/en\/manual\/payments\/shopify-payments\/faq#how-long-does-it-take-for-my-customer-to-get-refunded\" target=\"_blank\"\u003erefunds\u003c\/a\u003e.","created_at":"2024-03-08T13:18:00-05:00","test":true,"authorization":"","location_id":null,"user_id":73836855425,"parent_id":5489324621953,"processed_at":"2024-03-08T13:18:00-05:00","device_id":null,"error_code":null,"source_name":"1830279","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":"M","credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Davey Shafik","credit_card_wallet":null,"credit_card_expiration_month":1,"credit_card_expiration_year":2038,"payment_method_name":"visa"},"receipt":{},"amount":"460.31","currency":"USD","payments_refund_attributes":{"status":"deferred","acquirer_reference_number":null},"payment_id":"#1047.2","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid:\/\/shopify\/OrderTransaction\/5489326751873"}],"order_adjustments":[{"id":199565377665,"order_id":4181876637825,"refund_id":829178642561,"amount":"-13.48","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"-13.48","currency_code":"CAD"},"presentment_money":{"amount":"-10.00","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"id":199565410433,"order_id":4181876637825,"refund_id":829178642561,"amount":"620.62","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"620.62","currency_code":"CAD"},"presentment_money":{"amount":"460.31","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}}]}',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                'refundLineItems' => [
                    [
                        'refund_id' => $refund_id,
                        'line_item_id' => 10956880806017,
                        'quantity' => 1,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'deposit_customer_amount' => 4090,
                        'deposit_shop_amount' => 5514,
                        'discounts_customer_amount' => 0,
                        'discounts_shop_amount' => 0,
                        'gross_sales_customer_amount' => 40900,
                        'gross_sales_shop_amount' => 55144,
                        'is_tbyb' => 1,
                        'tax_customer_amount' => 4131,
                        'tax_shop_amount' => 5570,
                        'total_customer_amount' => 45031,
                        'total_shop_amount' => 60714,
                    ],
                ],
                'refund' => [
                    'id' => $refund_id,
                    'source_refund_reference_id' => 'gid://shopify/Refund/829178642561',
                    'order_id' => $order_id,
                    'store_id' => $store_id,
                    'shop_currency' => 'CAD',
                    'customer_currency' => 'USD',
                    'order_level_refund_customer_amount' => Money::ofMinor(0, 'USD'),
                    'order_level_refund_shop_amount' => Money::ofMinor(0, 'CAD'),
                    'tbyb_deposit_customer_amount' => 4090,
                    'tbyb_deposit_shop_amount' => 5514,
                    'tbyb_discounts_customer_amount' => 0,
                    'tbyb_discounts_shop_amount' => 0,
                    'tbyb_gross_sales_customer_amount' => 40900,
                    'tbyb_gross_sales_shop_amount' => 55144,
                    'tbyb_total_customer_amount' => Money::ofMinor(45031, 'USD'),
                    'tbyb_total_shop_amount' => Money::ofMinor(60714, 'CAD'),
                    'upfront_discounts_customer_amount' => 0,
                    'upfront_discounts_shop_amount' => 0,
                    'upfront_gross_sales_customer_amount' => 0,
                    'upfront_gross_sales_shop_amount' => 0,
                    'upfront_total_customer_amount' => Money::zero('USD'),
                    'upfront_total_shop_amount' => Money::zero('CAD'),
                    'refunded_customer_amount' => Money::ofMinor(46031, 'USD'),
                    'refunded_shop_amount' => Money::ofMinor(62062, 'CAD'),
                ],
            ],
            '[real] 1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: 1 upfront +additional ($10)' => [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => 'CAD',
                'customerCurrency' => 'USD',
                'lineItems' => [
                    [
                        'id' => 10956897747073,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956897747073',
                        'is_tbyb' => false,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 55144,
                        'price_customer_amount' => 40900,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 0,
                        'deposit_customer_amount' => 0,
                    ],
                    [
                        'id' => 10956907446401,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956907446401',
                        'is_tbyb' => true,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 68896,
                        'price_customer_amount' => 51100,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 6890,
                        'deposit_customer_amount' => 5110,
                    ],
                ],
                'webhookData' => json_decode(
                    '{"id":829178871937,"order_id":4181882372225,"created_at":"2024-03-08T13:27:35-05:00","note":"","user_id":73836855425,"processed_at":"2024-03-08T13:27:35-05:00","restock":true,"duties":[],"total_duties_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"additional_fees":[],"total_additional_fees_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"return":null,"refund_shipping_lines":[],"admin_graphql_api_id":"gid:\/\/shopify\/Refund\/829178871937","refund_line_items":[{"id":297508896897,"quantity":1,"line_item_id":10956897747073,"location_id":63239716993,"restock_type":"cancel","subtotal":551.44,"total_tax":55.7,"subtotal_set":{"shop_money":{"amount":"551.44","currency_code":"CAD"},"presentment_money":{"amount":"409.00","currency_code":"USD"}},"total_tax_set":{"shop_money":{"amount":"55.70","currency_code":"CAD"},"presentment_money":{"amount":"41.31","currency_code":"USD"}},"line_item":{"id":10956897747073,"variant_id":39732920746113,"title":"The Collection Snowboard: Hydrogen","quantity":1,"sku":"","variant_title":null,"vendor":"Hydrogen Vendor","fulfillment_service":"manual","product_id":6672585228417,"requires_shipping":true,"taxable":true,"gift_card":false,"name":"The Collection Snowboard: Hydrogen","variant_inventory_management":"shopify","properties":[],"product_exists":true,"fulfillable_quantity":0,"grams":0,"price":"551.44","total_discount":"0.00","fulfillment_status":null,"price_set":{"shop_money":{"amount":"551.44","currency_code":"CAD"},"presentment_money":{"amount":"409.00","currency_code":"USD"}},"total_discount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"discount_allocations":[],"duties":[],"admin_graphql_api_id":"gid:\/\/shopify\/LineItem\/10956897747073","tax_lines":[{"title":"Washington State Tax","price":"35.85","rate":0.065,"channel_liable":false,"price_set":{"shop_money":{"amount":"35.85","currency_code":"CAD"},"presentment_money":{"amount":"26.59","currency_code":"USD"}}},{"title":"Pierce County Tax","price":"0.00","rate":0.0,"channel_liable":false,"price_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"title":"Puyallup City Tax","price":"19.85","rate":0.036,"channel_liable":false,"price_set":{"shop_money":{"amount":"19.85","currency_code":"CAD"},"presentment_money":{"amount":"14.72","currency_code":"USD"}}}]}}],"transactions":[{"id":5489332027521,"order_id":4181882372225,"kind":"refund","gateway":"shopify_payments","status":"pending","message":"Refund submitted and processing. Learn more about \u003ca href=\"https:\/\/help.shopify.com\/en\/manual\/payments\/shopify-payments\/faq#how-long-does-it-take-for-my-customer-to-get-refunded\" target=\"_blank\"\u003erefunds\u003c\/a\u003e.","created_at":"2024-03-08T13:27:35-05:00","test":true,"authorization":"","location_id":null,"user_id":73836855425,"parent_id":5489331667073,"processed_at":"2024-03-08T13:27:35-05:00","device_id":null,"error_code":null,"source_name":"1830279","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":"M","credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Davey Shafik","credit_card_wallet":null,"credit_card_expiration_month":1,"credit_card_expiration_year":2038,"payment_method_name":"visa"},"receipt":{},"amount":"460.31","currency":"USD","payments_refund_attributes":{"status":"deferred","acquirer_reference_number":null},"payment_id":"#1048.2","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid:\/\/shopify\/OrderTransaction\/5489332027521"}],"order_adjustments":[{"id":199565639809,"order_id":4181882372225,"refund_id":829178871937,"amount":"-13.48","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"-13.48","currency_code":"CAD"},"presentment_money":{"amount":"-10.00","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"id":199565672577,"order_id":4181882372225,"refund_id":829178871937,"amount":"620.62","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"620.62","currency_code":"CAD"},"presentment_money":{"amount":"460.31","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}}]}',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                'refundLineItems' => [
                    [
                        'refund_id' => $refund_id,
                        'line_item_id' => 10956897747073,
                        'quantity' => 1,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'deposit_customer_amount' => 0,
                        'deposit_shop_amount' => 0,
                        'discounts_customer_amount' => 0,
                        'discounts_shop_amount' => 0,
                        'gross_sales_customer_amount' => 40900,
                        'gross_sales_shop_amount' => 55144,
                        'is_tbyb' => 0,
                        'tax_customer_amount' => 4131,
                        'tax_shop_amount' => 5570,
                        'total_customer_amount' => 45031,
                        'total_shop_amount' => 60714,
                    ],
                ],
                'refund' => [
                    'id' => $refund_id,
                    'source_refund_reference_id' => 'gid://shopify/Refund/829178871937',
                    'order_id' => $order_id,
                    'store_id' => $store_id,
                    'shop_currency' => 'CAD',
                    'customer_currency' => 'USD',
                    'order_level_refund_customer_amount' => Money::zero('USD'),
                    'order_level_refund_shop_amount' => Money::zero('CAD'),
                    'tbyb_deposit_customer_amount' => 0,
                    'tbyb_deposit_shop_amount' => 0,
                    'tbyb_discounts_customer_amount' => 0,
                    'tbyb_discounts_shop_amount' => 0,
                    'tbyb_gross_sales_customer_amount' => 0,
                    'tbyb_gross_sales_shop_amount' => 0,
                    'tbyb_total_customer_amount' => Money::zero('USD'),
                    'tbyb_total_shop_amount' => Money::zero('CAD'),
                    'upfront_discounts_customer_amount' => 0,
                    'upfront_discounts_shop_amount' => 0,
                    'upfront_gross_sales_customer_amount' => 40900,
                    'upfront_gross_sales_shop_amount' => 55144,
                    'upfront_total_customer_amount' => Money::ofMinor(45031, 'USD'),
                    'upfront_total_shop_amount' => Money::ofMinor(60714, 'CAD'),
                    'refunded_customer_amount' => Money::ofMinor(46031, 'USD'),
                    'refunded_shop_amount' => Money::ofMinor(62062, 'CAD'),
                ],
            ],
            '[real] 1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: +order ($460.31)' => [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => 'CAD',
                'customerCurrency' => 'USD',
                'lineItems' => [
                    [
                        'id' => 10956897747073,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956897747073',
                        'is_tbyb' => false,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 55144,
                        'price_customer_amount' => 40900,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 5514,
                        'deposit_customer_amount' => 4090,
                    ],
                    [
                        'id' => 10956907446401,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956907446401',
                        'is_tbyb' => true,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 68896,
                        'price_customer_amount' => 51100,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 0,
                        'deposit_customer_amount' => 0,
                    ],
                ],
                'webhookData' => json_decode(
                    '{"id":829178970241,"order_id":4181884272769,"created_at":"2024-03-08T13:30:38-05:00","note":"","user_id":73836855425,"processed_at":"2024-03-08T13:30:38-05:00","restock":false,"duties":[],"total_duties_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"additional_fees":[],"total_additional_fees_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"return":null,"refund_shipping_lines":[],"admin_graphql_api_id":"gid:\/\/shopify\/Refund\/829178970241","refund_line_items":[],"transactions":[{"id":5489334255745,"order_id":4181884272769,"kind":"refund","gateway":"shopify_payments","status":"pending","message":"Refund submitted and processing. Learn more about \u003ca href=\"https:\/\/help.shopify.com\/en\/manual\/payments\/shopify-payments\/faq#how-long-does-it-take-for-my-customer-to-get-refunded\" target=\"_blank\"\u003erefunds\u003c\/a\u003e.","created_at":"2024-03-08T13:30:37-05:00","test":true,"authorization":"","location_id":null,"user_id":73836855425,"parent_id":5489333796993,"processed_at":"2024-03-08T13:30:37-05:00","device_id":null,"error_code":null,"source_name":"1830279","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":"M","credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Davey Shafik","credit_card_wallet":null,"credit_card_expiration_month":1,"credit_card_expiration_year":2038,"payment_method_name":"visa"},"receipt":{},"amount":"460.31","currency":"USD","payments_refund_attributes":{"status":"deferred","acquirer_reference_number":null},"payment_id":"#1049.2","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid:\/\/shopify\/OrderTransaction\/5489334255745"}],"order_adjustments":[{"id":199565738113,"order_id":4181884272769,"refund_id":829178970241,"amount":"-620.62","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"-620.62","currency_code":"CAD"},"presentment_money":{"amount":"-460.31","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"id":199565770881,"order_id":4181884272769,"refund_id":829178970241,"amount":"620.62","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"620.62","currency_code":"CAD"},"presentment_money":{"amount":"460.31","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}}]}',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                'refundLineItems' => [],
                'refund' => [
                    'id' => $refund_id,
                    'source_refund_reference_id' => 'gid://shopify/Refund/829178970241',
                    'order_id' => $order_id,
                    'store_id' => $store_id,
                    'shop_currency' => 'CAD',
                    'customer_currency' => 'USD',
                    'order_level_refund_customer_amount' => Money::ofMinor(46031, 'USD'),
                    'order_level_refund_shop_amount' => Money::ofMinor(62062, 'CAD'),
                    'tbyb_deposit_customer_amount' => 0,
                    'tbyb_deposit_shop_amount' => 0,
                    'tbyb_discounts_customer_amount' => 0,
                    'tbyb_discounts_shop_amount' => 0,
                    'tbyb_gross_sales_customer_amount' => 0,
                    'tbyb_gross_sales_shop_amount' => 0,
                    'tbyb_total_customer_amount' => Money::zero('USD'),
                    'tbyb_total_shop_amount' => Money::zero('CAD'),
                    'upfront_discounts_customer_amount' => 0,
                    'upfront_discounts_shop_amount' => 0,
                    'upfront_gross_sales_customer_amount' => 0,
                    'upfront_gross_sales_shop_amount' => 0,
                    'upfront_total_customer_amount' => Money::zero('USD'),
                    'upfront_total_shop_amount' => Money::zero('USD'),
                    'refunded_customer_amount' => Money::ofMinor(46031, 'USD'),
                    'refunded_shop_amount' => Money::ofMinor(62062, 'CAD'),
                ],
            ],
            '[real] 1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: partial tbyb ($46.31)' => [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => 'CAD',
                'customerCurrency' => 'USD',
                'lineItems' => [
                    [
                        'id' => 10956907413633,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956907413633',
                        'is_tbyb' => true,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 55144,
                        'price_customer_amount' => 40900,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 5514,
                        'deposit_customer_amount' => 4090,
                    ],
                    [
                        'id' => 10956907446401,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956907446401',
                        'is_tbyb' => false,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 68896,
                        'price_customer_amount' => 51100,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 0,
                        'deposit_customer_amount' => 0,
                    ],
                ],
                'webhookData' => json_decode(
                    '{"id":829179134081,"order_id":4181885714561,"created_at":"2024-03-08T13:34:28-05:00","note":"","user_id":73836855425,"processed_at":"2024-03-08T13:34:28-05:00","restock":true,"duties":[],"total_duties_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"additional_fees":[],"total_additional_fees_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"return":null,"refund_shipping_lines":[],"admin_graphql_api_id":"gid:\/\/shopify\/Refund\/829179134081","refund_line_items":[{"id":297509060737,"quantity":1,"line_item_id":10956907413633,"location_id":63239716993,"restock_type":"cancel","subtotal":551.44,"total_tax":55.7,"subtotal_set":{"shop_money":{"amount":"551.44","currency_code":"CAD"},"presentment_money":{"amount":"409.00","currency_code":"USD"}},"total_tax_set":{"shop_money":{"amount":"55.70","currency_code":"CAD"},"presentment_money":{"amount":"41.31","currency_code":"USD"}},"line_item":{"id":10956907413633,"variant_id":39732920746113,"title":"The Collection Snowboard: Hydrogen","quantity":1,"sku":"","variant_title":null,"vendor":"Hydrogen Vendor","fulfillment_service":"manual","product_id":6672585228417,"requires_shipping":true,"taxable":true,"gift_card":false,"name":"The Collection Snowboard: Hydrogen","variant_inventory_management":"shopify","properties":[],"product_exists":true,"fulfillable_quantity":0,"grams":0,"price":"551.44","total_discount":"0.00","fulfillment_status":null,"price_set":{"shop_money":{"amount":"551.44","currency_code":"CAD"},"presentment_money":{"amount":"409.00","currency_code":"USD"}},"total_discount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"discount_allocations":[],"duties":[],"admin_graphql_api_id":"gid:\/\/shopify\/LineItem\/10956907413633","tax_lines":[{"title":"Washington State Tax","price":"35.85","rate":0.065,"channel_liable":false,"price_set":{"shop_money":{"amount":"35.85","currency_code":"CAD"},"presentment_money":{"amount":"26.59","currency_code":"USD"}}},{"title":"Pierce County Tax","price":"0.00","rate":0.0,"channel_liable":false,"price_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"title":"Puyallup City Tax","price":"19.85","rate":0.036,"channel_liable":false,"price_set":{"shop_money":{"amount":"19.85","currency_code":"CAD"},"presentment_money":{"amount":"14.72","currency_code":"USD"}}}]}}],"transactions":[{"id":5489337335937,"order_id":4181885714561,"kind":"refund","gateway":"shopify_payments","status":"pending","message":"Refund submitted and processing. Learn more about \u003ca href=\"https:\/\/help.shopify.com\/en\/manual\/payments\/shopify-payments\/faq#how-long-does-it-take-for-my-customer-to-get-refunded\" target=\"_blank\"\u003erefunds\u003c\/a\u003e.","created_at":"2024-03-08T13:34:27-05:00","test":true,"authorization":"","location_id":null,"user_id":73836855425,"parent_id":5489336385665,"processed_at":"2024-03-08T13:34:28-05:00","device_id":null,"error_code":null,"source_name":"1830279","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":"M","credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Davey Shafik","credit_card_wallet":null,"credit_card_expiration_month":1,"credit_card_expiration_year":2038,"payment_method_name":"visa"},"receipt":{},"amount":"46.31","currency":"USD","payments_refund_attributes":{"status":"deferred","acquirer_reference_number":null},"payment_id":"#1050.2","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid:\/\/shopify\/OrderTransaction\/5489337335937"}],"order_adjustments":[{"id":199565901953,"order_id":4181885714561,"refund_id":829179134081,"amount":"544.70","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"544.70","currency_code":"CAD"},"presentment_money":{"amount":"404.00","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"id":199565934721,"order_id":4181885714561,"refund_id":829179134081,"amount":"62.44","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"62.44","currency_code":"CAD"},"presentment_money":{"amount":"46.31","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}}]}',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                'refundLineItems' => [
                    [
                        'refund_id' => $refund_id,
                        'line_item_id' => 10956907413633,
                        'quantity' => 1,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'deposit_customer_amount' => 4090,
                        'deposit_shop_amount' => 5514,
                        'discounts_customer_amount' => 0,
                        'discounts_shop_amount' => 0,
                        'gross_sales_customer_amount' => 40900,
                        'gross_sales_shop_amount' => 55144,
                        'is_tbyb' => 1,
                        'tax_customer_amount' => 4131,
                        'tax_shop_amount' => 5570,
                        'total_customer_amount' => 45031,
                        'total_shop_amount' => 60714,
                    ],
                ],
                'refund' => [
                    'id' => $refund_id,
                    'source_refund_reference_id' => 'gid://shopify/Refund/829179134081',
                    'order_id' => $order_id,
                    'store_id' => $store_id,
                    'shop_currency' => 'CAD',
                    'customer_currency' => 'USD',
                    'order_level_refund_customer_amount' => Money::zero('USD'),
                    'order_level_refund_shop_amount' => Money::zero('CAD'),
                    'tbyb_deposit_customer_amount' => 4090,
                    'tbyb_deposit_shop_amount' => 5514,
                    'tbyb_discounts_customer_amount' => 0,
                    'tbyb_discounts_shop_amount' => 0,
                    'tbyb_gross_sales_customer_amount' => 40900,
                    'tbyb_gross_sales_shop_amount' => 55144,
                    'tbyb_total_customer_amount' => Money::ofMinor(45031, 'USD'),
                    'tbyb_total_shop_amount' => Money::ofMinor(60714, 'CAD'),
                    'upfront_discounts_customer_amount' => 0,
                    'upfront_discounts_shop_amount' => 0,
                    'upfront_gross_sales_customer_amount' => 0,
                    'upfront_gross_sales_shop_amount' => 0,
                    'upfront_total_customer_amount' => Money::zero('USD'),
                    'upfront_total_shop_amount' => Money::zero('CAD'),
                    'refunded_customer_amount' => Money::ofMinor(4631, 'USD'),
                    'refunded_shop_amount' => Money::ofMinor(6244, 'CAD'),
                ],
            ],
            '[real] 1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: +order ($46.31)' => [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => 'CAD',
                'customerCurrency' => 'USD',
                'lineItems' => [
                    [
                        'id' => 10956907413633,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956907413633',
                        'is_tbyb' => true,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 55144,
                        'price_customer_amount' => 40900,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 5514,
                        'deposit_customer_amount' => 4090,
                    ],
                    [
                        'id' => 10956907446401,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/10956907446401',
                        'is_tbyb' => false,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'price_shop_amount' => 68896,
                        'price_customer_amount' => 51100,
                        'discount_shop_amount' => 0,
                        'discount_customer_amount' => 0,
                        'deposit_shop_amount' => 0,
                        'deposit_customer_amount' => 0,
                    ],
                ],
                'webhookData' => json_decode(
                    '{"id":829179363457,"order_id":4181889286273,"created_at":"2024-03-08T13:41:28-05:00","note":"","user_id":73836855425,"processed_at":"2024-03-08T13:41:28-05:00","restock":false,"duties":[],"total_duties_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"additional_fees":[],"total_additional_fees_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"return":null,"refund_shipping_lines":[],"admin_graphql_api_id":"gid:\/\/shopify\/Refund\/829179363457","refund_line_items":[],"transactions":[{"id":5489341988993,"order_id":4181889286273,"kind":"refund","gateway":"shopify_payments","status":"pending","message":"Refund submitted and processing. Learn more about \u003ca href=\"https:\/\/help.shopify.com\/en\/manual\/payments\/shopify-payments\/faq#how-long-does-it-take-for-my-customer-to-get-refunded\" target=\"_blank\"\u003erefunds\u003c\/a\u003e.","created_at":"2024-03-08T13:41:27-05:00","test":true,"authorization":"","location_id":null,"user_id":73836855425,"parent_id":5489341104257,"processed_at":"2024-03-08T13:41:27-05:00","device_id":null,"error_code":null,"source_name":"1830279","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":"M","credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Davey Shafik","credit_card_wallet":null,"credit_card_expiration_month":1,"credit_card_expiration_year":2038,"payment_method_name":"visa"},"receipt":{},"amount":"46.31","currency":"USD","payments_refund_attributes":{"status":"deferred","acquirer_reference_number":null},"payment_id":"#1053.2","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid:\/\/shopify\/OrderTransaction\/5489341988993"}],"order_adjustments":[{"id":199566262401,"order_id":4181889286273,"refund_id":829179363457,"amount":"-62.44","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"-62.44","currency_code":"CAD"},"presentment_money":{"amount":"-46.31","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"id":199566295169,"order_id":4181889286273,"refund_id":829179363457,"amount":"62.44","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"62.44","currency_code":"CAD"},"presentment_money":{"amount":"46.31","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}}]}',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                'refundLineItems' => [
                ],
                'refund' => [
                    'id' => $refund_id,
                    'source_refund_reference_id' => 'gid://shopify/Refund/829179363457',
                    'order_id' => $order_id,
                    'store_id' => $store_id,
                    'shop_currency' => 'CAD',
                    'customer_currency' => 'USD',
                    'order_level_refund_customer_amount' => Money::ofMinor(4631, 'USD'),
                    'order_level_refund_shop_amount' => Money::ofMinor(6244, 'CAD'),
                    'tbyb_deposit_customer_amount' => 0,
                    'tbyb_deposit_shop_amount' => 0,
                    'tbyb_discounts_customer_amount' => 0,
                    'tbyb_discounts_shop_amount' => 0,
                    'tbyb_gross_sales_customer_amount' => 0,
                    'tbyb_gross_sales_shop_amount' => 0,
                    'tbyb_total_customer_amount' => Money::zero('USD'),
                    'tbyb_total_shop_amount' => Money::zero('CAD'),
                    'upfront_discounts_customer_amount' => 0,
                    'upfront_discounts_shop_amount' => 0,
                    'upfront_gross_sales_customer_amount' => 0,
                    'upfront_gross_sales_shop_amount' => 0,
                    'upfront_total_customer_amount' => Money::zero('USD'),
                    'upfront_total_shop_amount' => Money::zero('CAD'),
                    'refunded_customer_amount' => Money::ofMinor(4631, 'USD'),
                    'refunded_shop_amount' => Money::ofMinor(6244, 'CAD'),
                ],
            ],
            '[real] 1 tbyb (q: 3), +taxes, +discount, refund: 1 tbyb (q: 2)' => [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => 'CAD',
                'customerCurrency' => 'USD',
                'lineItems' => [
                    [
                        'id' => 10957452378241,
                        'order_id' => $order_id,
                        'quantity' => 3,
                        'source_id' => 'gid://shopify/LineItem/10957452378241',
                        'is_tbyb' => true,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'deposit_customer_amount' => 10220,
                        'deposit_shop_amount' => 13785,
                        'discount_customer_amount' => 15330,
                        'discount_shop_amount' => 20678,
                        'price_customer_amount' => 51100,
                        'price_shop_amount' => 68928,
                    ],
                ],
                'webhookData' => json_decode(
                    '{"id":829190242433,"order_id":4182097789057,"created_at":"2024-03-09T04:46:16-05:00","note":"","user_id":73836855425,"processed_at":"2024-03-09T04:46:16-05:00","restock":true,"duties":[],"total_duties_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"additional_fees":[],"total_additional_fees_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"return":null,"refund_shipping_lines":[],"admin_graphql_api_id":"gid:\/\/shopify\/Refund\/829190242433","refund_line_items":[{"id":297514729601,"quantity":2,"line_item_id":10957452378241,"location_id":63239716993,"restock_type":"cancel","subtotal":1240.71,"total_tax":125.31,"subtotal_set":{"shop_money":{"amount":"1240.71","currency_code":"CAD"},"presentment_money":{"amount":"919.80","currency_code":"USD"}},"total_tax_set":{"shop_money":{"amount":"125.31","currency_code":"CAD"},"presentment_money":{"amount":"92.90","currency_code":"USD"}},"line_item":{"id":10957452378241,"variant_id":39732921204865,"title":"The Collection Snowboard: Liquid","quantity":3,"sku":"","variant_title":null,"vendor":"Hydrogen Vendor","fulfillment_service":"manual","product_id":6672585523329,"requires_shipping":true,"taxable":true,"gift_card":false,"name":"The Collection Snowboard: Liquid","variant_inventory_management":"shopify","properties":[],"product_exists":true,"fulfillable_quantity":1,"grams":0,"price":"689.28","total_discount":"0.00","fulfillment_status":null,"price_set":{"shop_money":{"amount":"689.28","currency_code":"CAD"},"presentment_money":{"amount":"511.00","currency_code":"USD"}},"total_discount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"discount_allocations":[{"amount":"206.78","discount_application_index":0,"amount_set":{"shop_money":{"amount":"206.78","currency_code":"CAD"},"presentment_money":{"amount":"153.30","currency_code":"USD"}}}],"duties":[],"admin_graphql_api_id":"gid:\/\/shopify\/LineItem\/10957452378241","tax_lines":[{"title":"Washington State Tax","price":"120.97","rate":0.065,"channel_liable":false,"price_set":{"shop_money":{"amount":"120.97","currency_code":"CAD"},"presentment_money":{"amount":"89.68","currency_code":"USD"}}},{"title":"Pierce County Tax","price":"0.00","rate":0.0,"channel_liable":false,"price_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}},{"title":"Puyallup City Tax","price":"67.00","rate":0.036,"channel_liable":false,"price_set":{"shop_money":{"amount":"67.00","currency_code":"CAD"},"presentment_money":{"amount":"49.67","currency_code":"USD"}}}]}}],"transactions":[{"id":5489812930689,"order_id":4182097789057,"kind":"refund","gateway":"shopify_payments","status":"pending","message":"Refund submitted and processing. Learn more about \u003ca href=\"https:\/\/help.shopify.com\/en\/manual\/payments\/shopify-payments\/faq#how-long-does-it-take-for-my-customer-to-get-refunded\" target=\"_blank\"\u003erefunds\u003c\/a\u003e.","created_at":"2024-03-09T04:46:16-05:00","test":true,"authorization":"","location_id":null,"user_id":73836855425,"parent_id":5489812865153,"processed_at":"2024-03-09T04:46:16-05:00","device_id":null,"error_code":null,"source_name":"1830279","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":"M","credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Davey Shafik","credit_card_wallet":null,"credit_card_expiration_month":1,"credit_card_expiration_year":2038,"payment_method_name":"visa"},"receipt":{},"amount":"1012.70","currency":"USD","payments_refund_attributes":{"status":"deferred","acquirer_reference_number":null},"payment_id":"#1057.3","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid:\/\/shopify\/OrderTransaction\/5489812930689"}],"order_adjustments":[{"id":199576158337,"order_id":4182097789057,"refund_id":829190242433,"amount":"1366.02","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"1366.02","currency_code":"CAD"},"presentment_money":{"amount":"1012.70","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}}]}',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                'refundLineItems' => [
                    [
                        'refund_id' => $refund_id,
                        'line_item_id' => 10957452378241,
                        'quantity' => 2,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'deposit_customer_amount' => 6814,
                        'deposit_shop_amount' => 9190,
                        'discounts_customer_amount' => 10220,
                        'discounts_shop_amount' => 13785,
                        'gross_sales_customer_amount' => 102200,
                        'gross_sales_shop_amount' => 137856,
                        'is_tbyb' => 1,
                        'tax_customer_amount' => 9290,
                        'tax_shop_amount' => 12531,
                        'total_customer_amount' => 101270,
                        'total_shop_amount' => 136602,
                    ],
                ],
                'refund' => [
                    'id' => $refund_id,
                    'source_refund_reference_id' => 'gid://shopify/Refund/829190242433',
                    'order_id' => $order_id,
                    'store_id' => $store_id,
                    'shop_currency' => 'CAD',
                    'customer_currency' => 'USD',
                    'order_level_refund_customer_amount' => Money::zero('USD'),
                    'order_level_refund_shop_amount' => Money::zero('CAD'),
                    'tbyb_deposit_customer_amount' => 6814,
                    'tbyb_deposit_shop_amount' => 9190,
                    'tbyb_discounts_customer_amount' => 10220,
                    'tbyb_discounts_shop_amount' => 13785,
                    'tbyb_gross_sales_customer_amount' => 102200,
                    'tbyb_gross_sales_shop_amount' => 137856,
                    'tbyb_total_customer_amount' => Money::ofMinor(101270, 'USD'),
                    'tbyb_total_shop_amount' => Money::ofMinor(136602, 'CAD'),
                    'upfront_discounts_customer_amount' => 0,
                    'upfront_discounts_shop_amount' => 0,
                    'upfront_gross_sales_customer_amount' => 0,
                    'upfront_gross_sales_shop_amount' => 0,
                    'upfront_total_customer_amount' => Money::zero('USD'),
                    'upfront_total_shop_amount' => Money::zero('CAD'),
                    'refunded_customer_amount' => Money::ofMinor(101270, 'USD'),
                    'refunded_shop_amount' => Money::ofMinor(136602, 'CAD'),
                ],
            ],
            '[real] 1 tbyb (q: 1), 1 upfront (q: 1), +taxes, refund: $15 shipping' => [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => 'CAD',
                'customerCurrency' => 'USD',
                'lineItems' => [
                    [
                        'id' => 10957452378241,
                        'order_id' => $order_id,
                        'quantity' => 3,
                        'source_id' => 'gid://shopify/LineItem/10957452378241',
                        'is_tbyb' => true,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'deposit_customer_amount' => 10220,
                        'deposit_shop_amount' => 13785,
                        'discount_customer_amount' => 15330,
                        'discount_shop_amount' => 20678,
                        'price_customer_amount' => 51100,
                        'price_shop_amount' => 68928,
                    ],
                ],
                'webhookData' => json_decode(
                    '{"id":829233365121,"order_id":4182953590913,"created_at":"2024-03-11T22:28:29-04:00","note":"","user_id":73836855425,"processed_at":"2024-03-11T22:28:29-04:00","restock":false,"duties":[],"total_duties_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"additional_fees":[],"total_additional_fees_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"return":null,"refund_shipping_lines":[],"admin_graphql_api_id":"gid:\/\/shopify\/Refund\/829233365121","refund_line_items":[],"transactions":[{"id":5491147735169,"order_id":4182953590913,"kind":"refund","gateway":"shopify_payments","status":"pending","message":"Refund submitted and processing. Learn more about \u003ca href=\"https:\/\/help.shopify.com\/en\/manual\/payments\/shopify-payments\/faq#how-long-does-it-take-for-my-customer-to-get-refunded\" target=\"_blank\"\u003erefunds\u003c\/a\u003e.","created_at":"2024-03-11T22:28:28-04:00","test":true,"authorization":"","location_id":null,"user_id":73836855425,"parent_id":5491146719361,"processed_at":"2024-03-11T22:28:28-04:00","device_id":null,"error_code":null,"source_name":"1830279","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":"M","credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Davey Shafik","credit_card_wallet":null,"credit_card_expiration_month":1,"credit_card_expiration_year":2038,"payment_method_name":"visa"},"receipt":{},"amount":"16.52","currency":"USD","payments_refund_attributes":{"status":"deferred","acquirer_reference_number":null},"payment_id":"#1060.2","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid:\/\/shopify\/OrderTransaction\/5491147735169"}],"order_adjustments":[{"id":199613415553,"order_id":4182953590913,"refund_id":829233365121,"amount":"-20.21","tax_amount":"-2.05","kind":"shipping_refund","reason":"Shipping refund","amount_set":{"shop_money":{"amount":"-20.21","currency_code":"CAD"},"presentment_money":{"amount":"-15.00","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"-2.05","currency_code":"CAD"},"presentment_money":{"amount":"-1.52","currency_code":"USD"}}},{"id":199613448321,"order_id":4182953590913,"refund_id":829233365121,"amount":"22.26","tax_amount":"0.00","kind":"refund_discrepancy","reason":"Refund discrepancy","amount_set":{"shop_money":{"amount":"22.26","currency_code":"CAD"},"presentment_money":{"amount":"16.52","currency_code":"USD"}},"tax_amount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}}}]}',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                'refundLineItems' => [
                ],
                'refund' => [
                    'id' => $refund_id,
                    'source_refund_reference_id' => 'gid://shopify/Refund/829233365121',
                    'order_id' => $order_id,
                    'store_id' => $store_id,
                    'shop_currency' => 'CAD',
                    'customer_currency' => 'USD',
                    'order_level_refund_customer_amount' => Money::ofMinor(1652, 'USD'),
                    'order_level_refund_shop_amount' => Money::ofMinor(2226, 'CAD'),
                    'tbyb_deposit_customer_amount' => 0,
                    'tbyb_deposit_shop_amount' => 0,
                    'tbyb_discounts_customer_amount' => 0,
                    'tbyb_discounts_shop_amount' => 0,
                    'tbyb_gross_sales_customer_amount' => 0,
                    'tbyb_gross_sales_shop_amount' => 0,
                    'tbyb_total_customer_amount' => Money::zero('USD'),
                    'tbyb_total_shop_amount' => Money::zero('CAD'),
                    'upfront_discounts_customer_amount' => 0,
                    'upfront_discounts_shop_amount' => 0,
                    'upfront_gross_sales_customer_amount' => 0,
                    'upfront_gross_sales_shop_amount' => 0,
                    'upfront_total_customer_amount' => Money::zero('USD'),
                    'upfront_total_shop_amount' => Money::zero('CAD'),
                    'refunded_customer_amount' => Money::ofMinor(1652, 'USD'),
                    'refunded_shop_amount' => Money::ofMinor(2226, 'CAD'),
                ],
            ],
            '[real] 1 tbyb (q: 1), +taxes, order cancel, no order adjustment' => [
                'storeId' => $store_id,
                'orderId' => $order_id,
                'shopCurrency' => 'CAD',
                'customerCurrency' => 'USD',
                'lineItems' => [
                    [
                        'id' => 13145880232075,
                        'order_id' => $order_id,
                        'quantity' => 1,
                        'source_id' => 'gid://shopify/LineItem/13145880232075',
                        'is_tbyb' => true,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'deposit_customer_amount' => 5600,
                        'deposit_shop_amount' => 5600,
                        'discount_customer_amount' => 2500,
                        'discount_shop_amount' => 2500,
                        'price_customer_amount' => 56000,
                        'price_shop_amount' => 56000,
                    ],
                ],
                'webhookData' => json_decode(
                    '{"id":873035464843,"order_id":5218614313099,"created_at":"2024-03-27T18:17:11-04:00","note":"Order canceled","user_id":null,"processed_at":"2024-03-27T18:17:11-04:00","restock":true,"duties":[],"total_duties_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"return":null,"admin_graphql_api_id":"gid://shopify/Refund/873035464843","refund_line_items":[{"id":396338626699,"quantity":1,"line_item_id":13145880232075,"location_id":66545451147,"restock_type":"cancel","subtotal":535,"total_tax":69.55,"subtotal_set":{"shop_money":{"amount":"535.00","currency_code":"CAD"},"presentment_money":{"amount":"535.00","currency_code":"USD"}},"total_tax_set":{"shop_money":{"amount":"69.55","currency_code":"CAD"},"presentment_money":{"amount":"69.55","currency_code":"USD"}},"line_item":{"id":13145880232075,"variant_id":41739786453131,"title":"Blue Suit","quantity":1,"sku":"","variant_title":"blue","vendor":"Company 123","fulfillment_service":"manual","product_id":7224401100939,"requires_shipping":true,"taxable":true,"gift_card":false,"name":"Blue Suit - blue","variant_inventory_management":"shopify","properties":[],"product_exists":true,"fulfillable_quantity":0,"grams":0,"price":"560.00","total_discount":"0.00","fulfillment_status":null,"price_set":{"shop_money":{"amount":"560.00","currency_code":"CAD"},"presentment_money":{"amount":"560.00","currency_code":"USD"}},"total_discount_set":{"shop_money":{"amount":"0.00","currency_code":"CAD"},"presentment_money":{"amount":"0.00","currency_code":"USD"}},"discount_allocations":[{"amount":"25.00","discount_application_index":0,"amount_set":{"shop_money":{"amount":"25.00","currency_code":"CAD"},"presentment_money":{"amount":"25.00","currency_code":"USD"}}}],"duties":[],"admin_graphql_api_id":"gid://shopify/LineItem/13145880232075","tax_lines":[{"title":"HST","price":"69.55","rate":0.13,"channel_liable":false,"price_set":{"shop_money":{"amount":"69.55","currency_code":"CAD"},"presentment_money":{"amount":"69.55","currency_code":"USD"}}}]}}],"transactions":[{"id":6547630686347,"order_id":5218614313099,"kind":"void","gateway":"shopify_payments","status":"success","message":"Transaction approved","created_at":"2024-03-27T18:17:08-04:00","test":true,"authorization":"re_3Oz4ka2mxeptZ4mW1waJxvzi","location_id":null,"user_id":null,"parent_id":6547627147403,"processed_at":"2024-03-27T18:17:09-04:00","device_id":null,"error_code":null,"source_name":"64294584321","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":"M","credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Test Cancel","credit_card_wallet":null,"credit_card_expiration_month":4,"credit_card_expiration_year":2027,"payment_method_name":"visa"},"receipt":{"id":"re_3Oz4ka2mxeptZ4mW1waJxvzi","amount":5600,"balance_transaction":null,"charge":"ch_3Oz4ka2mxeptZ4mW13f0n5x0","object":"refund","reason":null,"status":"succeeded","created":1711577829,"currency":"USD","metadata":{},"payment_method_details":{"card":{"acquirer_reference_number":null,"acquirer_reference_number_status":"unavailable"},"type":"card"},"mit_params":{}},"amount":"0.00","currency":"USD","payments_refund_attributes":{"status":"success","acquirer_reference_number":null},"payment_id":"#1054.2","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid://shopify/OrderTransaction/6547630686347"},{"id":6547630784651,"order_id":5218614313099,"kind":"void","gateway":"shopify_payments","status":"success","message":"Transaction approved","created_at":"2024-03-27T18:17:10-04:00","test":true,"authorization":"re_3Oz4kk2mxeptZ4mW0zdcKDPJ","location_id":null,"user_id":null,"parent_id":6547627344011,"processed_at":"2024-03-27T18:17:11-04:00","device_id":null,"error_code":null,"source_name":"64294584321","payment_details":{"credit_card_bin":"424242","avs_result_code":"Y","cvv_result_code":null,"credit_card_number":"•••• •••• •••• 4242","credit_card_company":"Visa","buyer_action_info":null,"credit_card_name":"Test Cancel","credit_card_wallet":null,"credit_card_expiration_month":4,"credit_card_expiration_year":2027,"payment_method_name":"visa"},"receipt":{"id":"re_3Oz4kk2mxeptZ4mW0zdcKDPJ","amount":60455,"balance_transaction":null,"charge":"ch_3Oz4kk2mxeptZ4mW0WWqpoOg","object":"refund","reason":null,"status":"succeeded","created":1711577830,"currency":"USD","metadata":{},"payment_method_details":{"card":{"acquirer_reference_number":null,"acquirer_reference_number_status":"unavailable"},"type":"card"},"mit_params":{}},"amount":"0.00","currency":"USD","payments_refund_attributes":{"status":"success","acquirer_reference_number":null},"payment_id":"#1054.3","total_unsettled_set":{"presentment_money":{"amount":"0.0","currency":"USD"},"shop_money":{"amount":"0.0","currency":"CAD"}},"manual_payment_gateway":false,"admin_graphql_api_id":"gid://shopify/OrderTransaction/6547630784651"}],"order_adjustments":[]}',
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ),
                'refundLineItems' => [
                    [
                        'refund_id' => $refund_id,
                        'line_item_id' => 13145880232075,
                        'quantity' => 1,
                        'shop_currency' => 'CAD',
                        'customer_currency' => 'USD',
                        'deposit_customer_amount' => 5600,
                        'deposit_shop_amount' => 5600,
                        'discounts_customer_amount' => 2500,
                        'discounts_shop_amount' => 2500,
                        'gross_sales_customer_amount' => 56000,
                        'gross_sales_shop_amount' => 56000,
                        'is_tbyb' => 1,
                        'tax_customer_amount' => 6955,
                        'tax_shop_amount' => 6955,
                        'total_customer_amount' => 60455,
                        'total_shop_amount' => 60455,
                    ],
                ],
                'refund' => [
                    'id' => $refund_id,
                    'source_refund_reference_id' => 'gid://shopify/Refund/873035464843',
                    'order_id' => $order_id,
                    'store_id' => $store_id,
                    'shop_currency' => 'CAD',
                    'customer_currency' => 'USD',
                    'order_level_refund_customer_amount' => Money::ofMinor(0, 'USD'),
                    'order_level_refund_shop_amount' => Money::ofMinor(0, 'CAD'),
                    'tbyb_deposit_customer_amount' => 5600,
                    'tbyb_deposit_shop_amount' => 5600,
                    'tbyb_discounts_customer_amount' => 2500,
                    'tbyb_discounts_shop_amount' => 2500,
                    'tbyb_gross_sales_customer_amount' => 56000,
                    'tbyb_gross_sales_shop_amount' => 56000,
                    'tbyb_total_customer_amount' => Money::ofMinor(60455, 'USD'),
                    'tbyb_total_shop_amount' => Money::ofMinor(60455, 'USD'),
                    'upfront_discounts_customer_amount' => 0,
                    'upfront_discounts_shop_amount' => 0,
                    'upfront_gross_sales_customer_amount' => 0,
                    'upfront_gross_sales_shop_amount' => 0,
                    'upfront_total_customer_amount' => Money::zero('USD'),
                    'upfront_total_shop_amount' => Money::zero('CAD'),
                    'refunded_customer_amount' => Money::ofMinor(0, 'USD'),
                    'refunded_shop_amount' => Money::ofMinor(0, 'CAD'),
                ],
            ],
        ];
    }
}
