<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Shared\Values\Factory;
use Brick\Money\Money;
use Illuminate\Support\Facades\Date;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'order_id' => $this->faker->uuid(),
            'order_name' => '#' . $this->faker->randomNumber(5),
            'source_id' => 'gid://shopify/OrderTransaction/' . $this->faker->randomNumber(9),
            'source_order_id' => 'gid://shopify/Order/' . $this->faker->randomNumber(9),
            'shop_currency' => CurrencyAlpha3::US_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'shop_amount' => Money::ofMinor(10000, 'USD'),
            'customer_amount' => Money::ofMinor(10000, 'USD'),
            'unsettled_customer_amount' => Money::ofMinor(10000, 'USD'),
            'unsettled_shop_amount' => Money::ofMinor(10000, 'USD'),
            'kind' => TransactionKind::SALE,
            'gateway' => 'visa',
            'payment_id' => 'payment_id_123',
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => null,
            'status' => TransactionStatus::SUCCESS,
            'test' => false,
            'error_code' => null,
            'message' => null,
            'transaction_source_name' => 'web',
            'user_id' => null,
            'processed_at' => Date::now(),
            'authorization_expires_at' => null,
            'transaction_data' => [],
        ];
    }

    public function authorization(): static
    {
        return $this->state([
            'kind' => TransactionKind::AUTHORIZATION,
            'authorization_expires_at' => Date::now()->addDays(7),
        ]);
    }

    public function capture(): static
    {
        return $this->state([
            'kind' => TransactionKind::CAPTURE,
            'parent_transaction_id' => $this->faker->uuid(),
            'parent_transaction_source_id' => 'gid://shopify/OrderTransaction/123',
        ]);
    }
}
