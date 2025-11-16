<?php
declare(strict_types=1);

namespace App\Domain\Programs\Values;

use App\Domain\Programs\Enums\DepositType;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Illuminate\Validation\Rule;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class Program extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $storeId,
        public ?string $id = null,
        public string $name = 'Try Before You Buy',
        public ?string $shopifySellingPlanGroupId = null,
        public ?string $shopifySellingPlanId = null,
        public int $tryPeriodDays = 7,
        public DepositType $depositType = DepositType::PERCENTAGE,
        public int $depositValue = 10,
        public CurrencyAlpha3 $currency = CurrencyAlpha3::US_Dollar,
        public int $minTbybItems = 1,
        public ?int $maxTbybItems = null,
        public int $dropOffDays = 5
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'store_id' => ['required', 'string'],
            'name' => ['required', 'string', 'min:1'],
            'shopify_selling_plan_group_id' => ['nullable', 'string'],
            'shopify_selling_plan_id' => ['nullable', 'string'],
            'try_period_days' => ['required', 'int', 'min:1'],
            'deposit_type' => ['required', 'string', Rule::in([DepositType::FIXED, DepositType::PERCENTAGE])],
            'deposit_value' => ['required', 'int', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'min_tbyb_items' => ['required', 'int', 'min:1'],
            'max_tbyb_items' => ['nullable', 'int', 'gte:min_tbyb_items'],
            'drop_off_days' => ['int'],
        ];
    }
}
