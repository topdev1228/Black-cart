<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Trials\Enums\TrialStatus;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Trialable extends Value
{
    use HasValueFactory;

    protected array $hidden = [
        'id',
    ];

    public function __construct(
        public string $sourceKey,
        public string $sourceId,
        public TrialStatus $status = TrialStatus::INIT,
        public int $trialDuration = 7,
        public ?CarbonImmutable $expiresAt = null,
        public ?string $groupKey = null,
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $image = null,
        public ?string $id = null,
    ) {
    }
}
