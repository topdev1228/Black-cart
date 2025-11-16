<?php
declare(strict_types=1);

namespace App\Domain\Programs\Database\Factories;

use App\Domain\Programs\Enums\DepositType;
use Illuminate\Database\Eloquent\Factories\Factory;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Try Before You Buy',
            'shopify_selling_plan_group_id' => null,
            'shopify_selling_plan_id' => null,
            'try_period_days' => 7,
            'deposit_type' => DepositType::PERCENTAGE,
            'deposit_value' => 10,
            'currency' => CurrencyAlpha3::US_Dollar,
            'min_tbyb_items' => 1,
            'max_tbyb_items' => 4,
            'drop_off_days' => 5,
        ];
    }

    public function withShopifySellingPlanIds(): static
    {
        return $this->state([
            'shopify_selling_plan_group_id' => 'gid://shopify/SellingPlanGroup/12345',
            'shopify_selling_plan_id' => 'gid://shopify/SellingPlan/56789',
        ]);
    }

    public function unlimitedMaxTbybItems(): static
    {
        return $this->state([
            'max_tbyb_items' => null,
        ]);
    }

    public function fixedDeposit(): static
    {
        return $this->state([
            'deposit_type' => DepositType::FIXED,
            'deposit_value' => 2500,
        ]);
    }
}
