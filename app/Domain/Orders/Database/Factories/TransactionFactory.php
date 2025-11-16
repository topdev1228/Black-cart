<?php
declare(strict_types=1);

namespace App\Domain\Orders\Database\Factories;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'order_id' => Order::factory(),
            'order_name' => '#' . $this->faker->randomNumber(5),
            'source_id' => 'gid://shopify/OrderTransaction/' . $this->faker->randomNumber(9),
            'source_order_id' => 'gid://shopify/Order/' . $this->faker->randomNumber(9),
            'shop_currency' => 'USD',
            'customer_currency' => 'USD',
            'shop_amount' => 10000,
            'customer_amount' => 10000,
            'unsettled_customer_amount' => 0,
            'unsettled_shop_amount' => 0,
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
            'parent_transaction_source_id' => 'gid://shopify/OrderTransaction/' . $this->faker->randomNumber(9),
        ]);
    }
}
