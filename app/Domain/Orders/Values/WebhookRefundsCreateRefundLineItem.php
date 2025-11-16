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
class WebhookRefundsCreateRefundLineItem extends Value
{
    use HasValueFactory;

    public function __construct(
        #[MapName('id')]
        public string $sourceId,
        #[WithCast(StringTransform::class, 'shopifyGid:LineItem')]
        public string $lineItemId,
        public int $quantity,
        public string $restockType,
        public ShopifyMoneySetWithCurrencyCode $subtotalSet,
        public WebhookRefundsCreateLineItem $lineItem,
        public ?ShopifyMoneySetWithCurrencyCode $totalTaxSet = null,
    ) {
    }
}
