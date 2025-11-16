<?php

declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Shopify\Enums\SubscriptionStatus;
use App\Domain\Shopify\Values\Collections\SubscriptionLineItemCollection;
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
        public SubscriptionStatus $status = SubscriptionStatus::PENDING,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $activatedAt = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $deactivatedAt = null,
        #[DataCollectionOf(SubscriptionLineItem::class)]
        public ?SubscriptionLineItemCollection $lineItems = null,
    ) {
    }
}
