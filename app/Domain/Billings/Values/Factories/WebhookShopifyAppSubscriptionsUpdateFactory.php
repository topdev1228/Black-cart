<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values\Factories;

use App\Domain\Billings\Values\WebhookShopifyAppSubscription;
use App\Domain\Shared\Values\Factory;

class WebhookShopifyAppSubscriptionsUpdateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appSubscription' => WebhookShopifyAppSubscription::builder()->create(),
        ];
    }
}
