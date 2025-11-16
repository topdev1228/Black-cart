<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @see TrialGroupExpiredEvent
 */
#[MapName(SnakeCaseMapper::class)]
class TrialGroupExpiredEvent extends Value
{
    use HasValueFactory;

    public function __construct(
        public ?string $groupKey = null,
    ) {
    }
}
