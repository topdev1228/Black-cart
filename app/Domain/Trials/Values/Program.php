<?php
declare(strict_types=1);

namespace App\Domain\Trials\Values;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Trials\Enums\DepositType;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Program extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $id,
        public string $storeId,
        public string $shopifySellingPlanGroupId,
        public string $shopifySellingPlanId,
        public string $name,
        public int $tryPeriodDays,
        public DepositType $depositType,
        public int $depositValue,
        public CurrencyAlpha3 $currency,
        public int $dropOffDays
    ) {
    }
}
