<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\StringTransform;
use App\Domain\Shared\Values\Value;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookRefundsCreateTransaction extends Value
{
    use HasValueFactory;

    public function __construct(
        #[WithCast(StringTransform::class, 'shopifyGid:Order')]
        public string $orderId,
        public CurrencyAlpha3 $currency,
    ) {
    }
}
