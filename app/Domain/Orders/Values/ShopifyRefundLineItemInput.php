<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Enums\ShopifyRefundLineItemRestockType;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ShopifyRefundLineItemInput extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $lineItemId,
        public ShopifyRefundLineItemRestockType $restockType,
        public int $quantity = 1,
        public ?string $locationId = null,
    ) {
    }
}
