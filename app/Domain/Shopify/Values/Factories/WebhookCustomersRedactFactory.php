<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values\Factories;

use App\Domain\Shared\Values\Factory;

class WebhookCustomersRedactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id' => '1234567890',
            'shop_domain' => 'testshop.myshopify.com',
            'customer' => [
                'id' => 9876543210,
                'email' => 'shopper@example.com',
                'phone' => '555-555-5555',
            ],
            'orders_to_redact' => [
                123,
                456,
                789,
            ],
        ];
    }
}
