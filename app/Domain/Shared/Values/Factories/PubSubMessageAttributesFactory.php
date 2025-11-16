<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values\Factories;

use App\Domain\Shared\Values\Factory;
use Illuminate\Support\Facades\Date;

class PubSubMessageAttributesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event' => $this->faker->word(),
            'publishedAt' => Date::now(),
            'uuid' => $this->faker->uuid(),
            'domain' => $this->faker->domainName(),
        ];
    }

    public function shopifyWebhook(): static
    {
        return $this->state([
            'X-Shopify-Topic' => 'orders/create',
            'X-Shopify-Hmac-Sha256' => 'XWmrwMey6OsLMeiZKwP4FppHH3cmAiiJJAweH5Jo4bM=',
            'X-Shopify-Shop-Domain' => 'example.myshopify.com',
            'X-Shopify-API-Version' => '2023-10',
            'X-Shopify-Webhook-Id' => 'b54557e4-bdd9-4b37-8a5f-bf7d70bcd043',
            'X-Shopify-Triggered-At' => '2023-03-29T18:00:27.877041743Z',
        ]);
    }
}
