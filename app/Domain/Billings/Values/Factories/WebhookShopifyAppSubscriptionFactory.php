<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values\Factories;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Shared\Values\Factory;
use Illuminate\Support\Facades\Date;

class WebhookShopifyAppSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => 'gid://shopify/AppSubscription/' . $this->faker->uuid(),
            'name' => 'Subscription in pending status',
            'status' => SubscriptionStatus::PENDING,
            'shopId' => 'gid://shopify/Shop/' . $this->faker->uuid(),
            'cappedAmount' => '20.0',
            'currency' => 'USD',
            'createdAt' => Date::now(),
            'updatedAt' => Date::now(),
        ];
    }
}
