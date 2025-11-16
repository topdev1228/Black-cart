<?php
declare(strict_types=1);

namespace App\Domain\Stores\Database\Factories;

use App\Domain\Stores\Enums\EcommercePlatform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class StoreFactory extends Factory
{
    public function definition(): array
    {
        return [
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
            'ecommerce_platform_store_id' => (string) ($this->faker->randomNumber(6)),
            'ecommerce_platform_plan' => 'partner_test',
            'ecommerce_platform_plan_name' => 'Developer Preview',
            'source' => 'google',
            'created_at' => Date::now(),
        ];
    }
}
