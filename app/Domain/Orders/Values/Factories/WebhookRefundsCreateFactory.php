<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Values\WebhookRefundsCreateDuties;
use App\Domain\Orders\Values\WebhookRefundsCreateOrderAdjustment;
use App\Domain\Orders\Values\WebhookRefundsCreateRefundLineItem;
use App\Domain\Orders\Values\WebhookRefundsCreateTransaction;
use App\Domain\Shared\Values\Factory;

class WebhookRefundsCreateFactory extends Factory
{
    public function definition(): array
    {
        $sourceOrderId = 'gid://shopify/Orders/' . $this->faker->randomNumber(8);

        return [
            'sourceId' => 'gid://shopify/Refund/' . $this->faker->randomNumber(8),
            'sourceOrderId' => $sourceOrderId,
            'duties' => WebhookRefundsCreateDuties::builder()->create(),
            'orderLevelRefundAdjustments' => WebhookRefundsCreateOrderAdjustment::collection([
                WebhookRefundsCreateOrderAdjustment::builder()->create([
                    'orderId' => $sourceOrderId,
                ]),
            ]),
            'refundLineItems' => WebhookRefundsCreateRefundLineItem::collection([WebhookRefundsCreateRefundLineItem::builder()->create()]),
            'transactions' => rand(0, 1)
                ? WebhookRefundsCreateTransaction::collection([])
                : WebhookRefundsCreateTransaction::collection([
                    WebhookRefundsCreateTransaction::builder()->create([
                        'orderId' => $sourceOrderId,
                    ]),
                ]),
        ];
    }
}
