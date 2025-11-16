<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values\Factories;

use App\Domain\Shared\Values\Factory;

class WebhookShopRedactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id' => '1234567890',
            'shop_domain' => 'testshop.myshopify.com',
        ];
    }
}
