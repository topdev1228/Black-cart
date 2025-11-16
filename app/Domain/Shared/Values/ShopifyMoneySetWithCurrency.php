<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ShopifyMoneySetWithCurrency extends Value
{
    use HasValueFactory;

    public function __construct(
        public ShopifyMoneyWithCurrency $shopMoney,
        public ShopifyMoneyWithCurrency $presentmentMoney,
    ) {
    }
}
