<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Shopify\Enums\JobErrorCode;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Enums\JobType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class Job extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $storeId,
        public string $query,
        public string $domain,
        public string $topic,
        public JobType $type = JobType::QUERY,
        public ?string $id = null,
        public ?string $shopifyJobId = null,
        public ?string $exportFileUrl = null,
        public ?string $exportPartialFileUrl = null,
        public ?JobStatus $status = null,
        public ?JobErrorCode $errorCode = null,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'store_id' => ['required', 'string'],
            'query' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in([
                JobType::QUERY,
                JobType::MUTATION,
            ])],
            'domain' => ['required', 'string', Rule::in([
                'billings',
                'orders',
                'payments',
                'products',
                'programs',
                'shopify',
                'stores',
                'trials',
            ])],
            'topic' => ['required', 'string'],
            'shopify_job_id' => ['nullable', 'string'],
            'export_file_url' => ['nullable', 'string'],
            'export_partial_file_url' => ['nullable', 'string'],
            'status' => ['nullable', 'string', Rule::in([
                JobStatus::CANCELED,
                JobStatus::CANCELING,
                JobStatus::COMPLETED,
                JobStatus::CREATED,
                JobStatus::EXPIRED,
                JobStatus::FAILED,
                JobStatus::RUNNING,
            ])],
            'errorCode' => ['nullable', 'string', Rule::in([
                JobErrorCode::ACCESS_DENIED,
                JobErrorCode::INTERNAL_SERVER_ERROR,
                JobErrorCode::TIMEOUT,
                JobErrorCode::INTERNAL_FILE_SERVER_ERROR,
                JobErrorCode::INVALID_MUTATION,
                JobErrorCode::INVALID_STAGED_UPLOAD_FILE,
                JobErrorCode::NO_SUCH_FILE,
                JobErrorCode::OPERATION_IN_PROGRESS,
            ])],
        ];
    }
}
