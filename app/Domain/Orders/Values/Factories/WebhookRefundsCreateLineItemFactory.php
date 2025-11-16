<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Values\WebhookRefundsCreateLineItemDiscountAllocation;
use App\Domain\Orders\Values\WebhookRefundsCreateLineItemTaxLine;
use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\ShopifyMoneySetWithCurrencyCode;

class WebhookRefundsCreateLineItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'variantId' => $this->faker->uuid(),
            'title' => $this->faker->word(),
            'quantity' => $this->faker->randomNumber(),
            'sku' => $this->faker->word(),
            'variantTitle' => $this->faker->word(),
            'vendor' => $this->faker->word(),
            'fulfillmentService' => $this->faker->word(),
            'productId' => $this->faker->uuid(),
            'requiresShipping' => $this->faker->boolean(),
            'taxable' => $this->faker->boolean(),
            'giftCard' => $this->faker->boolean(),
            'name' => $this->faker->word(),
            'variantInventoryManagement' => $this->faker->word(),
            'properties' => $this->faker->words(),
            'productExists' => $this->faker->boolean(),
            'fulfillableQuantity' => $this->faker->randomNumber(),
            'grams' => $this->faker->randomNumber(),
            'price' => $this->faker->word(),
            'totalDiscount' => $this->faker->word(),
            'fulfillmentStatus' => $this->faker->word(),
            'priceSet' => ShopifyMoneySetWithCurrencyCode::builder()->create(),
            'totalDiscountSet' => ShopifyMoneySetWithCurrencyCode::builder()->create(),
            'discountAllocations' => WebhookRefundsCreateLineItemDiscountAllocation::collection([WebhookRefundsCreateLineItemDiscountAllocation::builder()->create()]),
            'taxLines' => WebhookRefundsCreateLineItemTaxLine::collection([WebhookRefundsCreateLineItemTaxLine::builder()->create()]),
        ];
    }
}
