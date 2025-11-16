<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Values\Collections\AnalyticsDataRecordCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class AnalyticsData extends Value
{
    use HasValueFactory;

    public function __construct(
        #[DataCollectionOf(AnalyticsDataRecord::class)]
        public AnalyticsDataRecordCollection $data
    ) {
    }
}
