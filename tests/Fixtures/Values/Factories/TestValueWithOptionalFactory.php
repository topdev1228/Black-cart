<?php
declare(strict_types=1);

namespace Tests\Fixtures\Values\Factories;

use App\Domain\Shared\Values\Factory;

class TestValueWithOptionalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'test',
            'nickname' => 'testing',
        ];
    }
}
