<?php
declare(strict_types=1);

namespace App\Domain\Stores\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Stores\Enums\EcommercePlatform;
use Illuminate\Support\Facades\Date;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class StoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'accessToken' => $this->faker->uuid(),
            'name' => $this->faker->company(),
            'domain' => $this->faker->unique()->domainName(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'owner_name' => $this->faker->name(),
            'currency' => CurrencyAlpha3::US_Dollar,
            'primary_locale' => 'en',
            'address1' => $this->faker->streetAddress(),
            'address2' => null,
            'city' => 'New York',
            'state' => 'New York',
            'state_code' => 'NY',
            'country' => 'US',
            'country_code' => 'US',
            'country_name' => 'United States',
            'iana_timezone' => 'America/New_York',
            'ecommerce_platform' => EcommercePlatform::SHOPIFY,
            'ecommerce_platform_store_id' => $this->faker->randomNumber(),
            'ecommerce_platform_plan' => 'partner_test',
            'ecommerce_platform_plan_name' => 'Developer Preview',
            'source' => 'google',
            'created_at' => Date::now(),
        ];
    }

    public function null(): static
    {
        return $this->state([
            'email' => null,
            'phone' => null,
            'owner_name' => null,
            'currency' => null,
            'primary_locale' => null,
            'address1' => null,
            'address2' => null,
            'city' => null,
            'state' => null,
            'state_code' => null,
            'country' => null,
            'country_code' => null,
            'country_name' => null,
            'iana_timezone' => null,
            'ecommerce_platform_plan' => null,
            'ecommerce_platform_plan_name' => null,
            'source' => null,
            'created_at' => null,
        ]);
    }

    public function shopifyPlus()
    {
        return $this->state([
            'ecommerce_platform_plan' => 'shopify_plus',
            'ecommerce_platform_plan_name' => 'Shopify Plus',
        ]);
    }

    public function shopifyBasic()
    {
        return $this->state([
            'ecommerce_platform_plan' => 'basic',
            'ecommerce_platform_plan_name' => 'Basic',
        ]);
    }
}
