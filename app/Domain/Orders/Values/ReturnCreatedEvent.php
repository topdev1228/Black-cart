<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @see App\Domain\Orders\Events\ReturnCreatedEvent
 */
#[MapName(SnakeCaseMapper::class)]
class ReturnCreatedEvent extends Value
{
    public function __construct(
        public OrderReturn $return,
    ) {
    }
}
