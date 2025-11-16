<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\StringTransform;
use App\Domain\Shared\Values\ShopifyMoneySetWithCurrencyCode;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookRefundsCreateOrderAdjustment extends Value
{
    use HasValueFactory;

    public function __construct(
        #[WithCast(StringTransform::class, 'shopifyGid:Order')]
        public string $orderId,
        public ShopifyMoneySetWithCurrencyCode $amountSet,
        public ?ShopifyMoneySetWithCurrencyCode $taxAmountSet = null,
    ) {
    }
}
