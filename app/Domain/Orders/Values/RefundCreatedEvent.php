<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @see App\Domain\Orders\Events\RefundCreatedEvent
 */
#[MapName(SnakeCaseMapper::class)]
class RefundCreatedEvent extends Value
{
    use HasValueFactory;

    public function __construct(
        public Refund $refund,
    ) {
    }
}
