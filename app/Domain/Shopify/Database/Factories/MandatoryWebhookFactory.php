<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Database\Factories;

use App\Domain\Shopify\Enums\MandatoryWebhookStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class MandatoryWebhookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => 'blackcart_store_id_123',
            'topic' => 'shop/redact',
            'shopify_shop_id' => '1234567890',
            'shopify_domain' =>'testshop.myshopify.com',
            'data' => [
                'shop_id' => '1234567890',
                'shop_domain' => 'testshop.myshopify.com',
            ],
            'status' => MandatoryWebhookStatus::PENDING,
        ];
    }

    public function customersRedact(): static
    {
        return $this->state([
            'topic' => 'customers/redact',
            'data' => [
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
            ],
        ]);
    }

    public function customersDataRequest(): static
    {
        return $this->state([
            'topic' => 'customers/data_request',
            'data' => [
                'shop_id' => '1234567890',
                'shop_domain' => 'testshop.myshopify.com',
                'orders_requested' => [
                    123,
                    456,
                    789,
                ],
                'customer' => [
                    'id' => 9876543210,
                    'email' => 'shopper@example.com',
                    'phone' => '555-555-5555',
                ],
                'data_request' => [
                    'id' => 9999,
                ],
            ],
        ]);
    }
}
