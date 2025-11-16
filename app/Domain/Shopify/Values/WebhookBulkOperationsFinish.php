<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\SafeEnum;
use App\Domain\Shared\Values\Value;
use App\Domain\Shopify\Enums\JobErrorCode;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Enums\JobType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookBulkOperationsFinish extends Value
{
    use HasValueFactory;

    //    {
    //        "admin_graphql_api_id": "gid://shopify/BulkOperation/147595010",
    //        "completed_at": "2024-01-09T05:54:12-05:00",
    //        "created_at": "2024-01-09T05:54:12-05:00",
    //        "error_code": null,
    //        "status": "completed",
    //        "type": "query"
    //    }

    public function __construct(
        public string $adminGraphqlApiId,
        public CarbonImmutable $completedAt,
        public CarbonImmutable $createdAt,
        public JobStatus $status,
        public JobType $type,
        #[WithCast(SafeEnum::class, 'lower')]
        public ?JobErrorCode $errorCode = null,
    ) {
    }
}
