<?php
declare(strict_types=1);

namespace App\Domain\Payments\Values\Factories;

use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Payments\Repositories\TransactionRepository;
use App\Domain\Shared\Values\Factory;
use Illuminate\Support\Facades\Date;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'order_id' => $this->faker->uuid(),
            'source_id' => 'gid://shopify/OrderTransaction/' . $this->faker->randomNumber(9),
            'source_order_id' => 'gid://shopify/Order/' . $this->faker->randomNumber(9),
            'kind' => TransactionKind::AUTHORIZATION,
            'status' => TransactionStatus::SUCCESS,
            'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
            'shop_currency' => 'USD',
            'customer_currency' => 'USD',
            'shop_amount' => 10000,
            'customer_amount' => 10000,
            'authorization_expires_at' => Date::now()->addDays(7)->format('Y-m-d\TH:i:sP'),
            'captured_transaction_id' => null,
            'captured_transaction_source_id' => null,
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => null,
        ];
    }

    public function capture(): static
    {
        return $this->state([
            'authorization_expires_at' => null,
            'kind' => TransactionKind::CAPTURE,
            'parent_transaction_id' => $this->faker->uuid(),
            'parent_transaction_source_id' => 'gid://shopify/OrderTransaction/' . $this->faker->randomNumber(9),
        ]);
    }

    public function captured(): static
    {
        return $this->state([
            'captured_transaction_id' => $this->faker->uuid(),
            'captured_transaction_source_id' => 'gid://shopify/OrderTransaction/' . $this->faker->randomNumber(9),
        ]);
    }
}
