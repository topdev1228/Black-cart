<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\SafeEnum;
use App\Domain\Shared\Values\Value;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Subscription extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $id,
        public string $storeId,
        #[WithCast(SafeEnum::class, 'lower')]
        public SubscriptionStatus $status = SubscriptionStatus::PENDING,
        public CarbonImmutable|Carbon|null $currentPeriodStart = null,
        public CarbonImmutable|Carbon|null $currentPeriodEnd = null,
        public int $trialDays = 0,
        public CarbonImmutable|Carbon|null $trialPeriodEnd = null,
        public bool $isTest = false,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $activatedAt = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $deactivatedAt = null,
    ) {
    }
}
