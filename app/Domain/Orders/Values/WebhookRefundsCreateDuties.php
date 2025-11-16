<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookRefundsCreateDuties extends Value
{
    use HasValueFactory;

    public function __construct(
        #[DataCollectionOf(WebhookRefundsCreateDuty::class)]
        public ?DataCollection $duties = null,
    ) {
    }
}
