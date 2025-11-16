<?php
declare(strict_types=1);

namespace App\Domain\Stores\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Stores\Values\Collections\StoreSettingCollection;
use App\Domain\Stores\Values\StoreSetting;

class StoreSettingsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'settings' => new StoreSettingCollection(StoreSetting::class, null),
        ];
    }
}
