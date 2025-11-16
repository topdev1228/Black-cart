<?php
declare(strict_types=1);

namespace App\Domain\Orders\Database\Factories;

use App\Domain\Orders\Enums\DepositType;
use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Enums\LineItemStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Str;

class LineItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => $this->faker->uuid(),
            'source_order_id' => Str::shopifyGid($this->faker->uuid(), 'Order'),
            'source_id' => Str::shopifyGid($this->faker->uuid(), 'LineItem'),
            'source_product_id' => Str::shopifyGid($this->faker->uuid(), 'Product'),
            'source_variant_id' => Str::shopifyGid($this->faker->uuid(), 'Variant'),
            'product_title' => $this->faker->words(3, true),
            'variant_title' => $this->faker->words(2, true),
            'thumbnail' => 'https://example.com/image.jpg',
            'quantity' => 1,
            'original_quantity' => 1,
            'status' => LineItemStatus::OPEN,
            'decision_status' => LineItemDecisionStatus::KEPT,
            'shop_currency' => CurrencyAlpha3::US_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'price_shop_amount' => 1000,
            'price_customer_amount' => 1000,
            'is_tbyb' => true,
            'selling_plan_id' => null,
            'deposit_type' => null,
            'deposit_value' => 0,
            'deposit_shop_amount' => 0,
            'deposit_customer_amount' => 0,
        ];
    }

    public function upfront(): static
    {
        return $this->state([
            'is_tbyb' => false,
            'deposit_type' => null,
            'deposit_value' => 0,
            'deposit_shop_amount' => 0,
            'deposit_customer_amount' => 0,
        ]);
    }

    public function withPercentageDeposit(int $depositPercentage = 10): static
    {
        return $this->state([
            'selling_plan_id' => $this->faker->uuid(),
            'deposit_type' => DepositType::PERCENTAGE,
            'deposit_value' => $depositPercentage,
            'deposit_shop_amount' => $depositPercentage * 1000, // based on the default line item price of 1000 cents
            'deposit_customer_amount' => $depositPercentage * 1000, // based on the default line item price of 1000 cents
        ]);
    }

    public function withFixedDeposit(int $depositAmountInCents = 100): static
    {
        return $this->state([
            'selling_plan_id' => $this->faker->uuid(),
            'deposit_type' => DepositType::FIXED,
            'deposit_value' => $depositAmountInCents,
            'deposit_shop_amount' => $depositAmountInCents,
            'deposit_customer_amount' => $depositAmountInCents,
        ]);
    }
}
