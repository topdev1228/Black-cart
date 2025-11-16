<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Shopify\Enums\DepositType;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Program extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $storeId,
        public ?string $id = null,
        public string $name = 'Try Before You Buy',
        public ?string $shopifySellingPlanGroupId = null,
        public ?string $shopifySellingPlanId = null,
        public int $tryPeriodDays = 7,
        public DepositType $depositType = DepositType::PERCENTAGE,
        public int $depositValue = 0,
        public CurrencyAlpha3 $currency = CurrencyAlpha3::US_Dollar,
        public int $minTbybItems = 1,
        public ?int $maxTbybItems = null,
    ) {
    }
}
