<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Values\LineItem as LineItemValue;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @see App\Domain\Orders\Events\LineItemSavedEvent
 */
#[MapName(SnakeCaseMapper::class)]
class LineItemSavedEvent extends Value
{
    public function __construct(
        public LineItemValue $lineItem,
    ) {
    }
}
