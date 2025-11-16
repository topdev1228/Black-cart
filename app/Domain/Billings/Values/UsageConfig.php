<?php

declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Billings\Values\Collections\UsageConfigEntryCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UsageConfig extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $storeId,
        public string $subscriptionLineItemId,
        public string $description,
        #[DataCollectionOf(UsageConfigEntry::class)]
        public UsageConfigEntryCollection $config,
        public CurrencyAlpha3 $currency,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable $validFrom,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?CarbonImmutable $validTo = null,
    ) {
    }
}
