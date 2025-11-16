<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class TrialStartedEvent extends Value
{
    /**
     * Create a new event instance.
     */
    public function __construct(public Trialable $trialable)
    {
    }
}
