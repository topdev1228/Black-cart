<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Programs\Traits;

use App\Domain\Programs\Enums\DepositType;

trait ProgramConfigurationsTestData
{
    public static function programConfigurationsProvider(): array
    {
        return [
            'No deposit 7-day trial' => [
                7, DepositType::FIXED, 0,
            ],
            'Fixed deposit 14-day trial' => [
                14, DepositType::FIXED, 5000,
            ],
            'Percentage deposit 35-day trial' => [
                35, DepositType::PERCENTAGE, 10,
            ],
        ];
    }
}
