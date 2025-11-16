<?php
declare(strict_types=1);

namespace App\Domain\Trials\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Trials\Enums\TrialStatus;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class Trialable extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $sourceKey,
        public string $sourceId,
        public TrialStatus $status = TrialStatus::INIT,
        public int $trialDuration = 7,
        public CarbonImmutable|Carbon|null $expiresAt = null,
        public ?string $groupKey = null,
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $image = null,
        public ?string $id = null,
    ) {
    }

    public function readyForTrial(): bool
    {
        return $this->status === TrialStatus::PRETRIAL;
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'source_key' => ['required', 'string'],
            'source_id' => ['required', 'string'],
            'status' => ['string', 'nullable'],
            'group_key' => ['string', 'nullable'],
            'trial_duration' => ['integer'],
            'expires_at' => ['datetime', 'nullable'],
            'title' => ['string', 'nullable'],
            'subtitle' => ['string', 'nullable'],
            'image' => ['string', 'nullable'],
        ];
    }
}
