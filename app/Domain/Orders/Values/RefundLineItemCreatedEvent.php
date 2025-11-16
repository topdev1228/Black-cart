<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Values\RefundLineItem as RefundLineItemValue;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @see App\Domain\Orders\Events\RefundLineItemCreatedEvent
 */
#[MapName(SnakeCaseMapper::class)]
class RefundLineItemCreatedEvent extends Value
{
    public function __construct(
        public RefundLineItemValue $refundLineItemValue,
    ) {
    }
}
