<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Values\Store;

class AppContextFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jwtToken' => JwtToken::empty(),
            'store' => Store::builder()->create(),
        ];
    }
}
