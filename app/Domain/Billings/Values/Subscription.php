<?php

declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Values\Collections\SubscriptionLineItemCollection;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\SafeEnum;
use App\Domain\Shared\Values\Value;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Subscription extends Value
{
    use HasValueFactory;
    use HasValueCollection;

    public function __construct(
        public string $storeId,
        public ?string $id = null,
        public ?string $shopifyAppSubscriptionId = null,
        public ?string $shopifyConfirmationUrl = null,
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
        #[DataCollectionOf(SubscriptionLineItem::class)]
        public ?SubscriptionLineItemCollection $subscriptionLineItems = null,
    ) {
    }

    public function isActive(): bool
    {
        return true; // todo validate this
    }
}
