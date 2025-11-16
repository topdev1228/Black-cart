<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values\Factories;

use App\Domain\Shared\Values\Factory;

class MetafieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => 'name',
            'value' => 'Try Before You Buy',
            'type' => 'single_line_text_field',
            'id' => null,
        ];
    }

    public function integer(string $key, int|string $value): static
    {
        return $this->state([
            'key' => $key,
            'value' => (string) $value,
            'type' => 'number_integer',
        ]);
    }

    public function string(string $key, string $value): static
    {
        return $this->state([
            'key' => $key,
            'value' => $value,
            'type' => 'single_line_text_field',
        ]);
    }

    public function money(string $key, float $amount, string $currency): static
    {
        return $this->state([
            'key' => $key,
            'value' => json_encode([
                'amount' => $amount,
                'currency_code' => $currency,
            ]),
            'type' => 'money',
        ]);
    }
}
