<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Models;

use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use App\Domain\Shopify\Enums\JobErrorCode;
use App\Domain\Shopify\Enums\JobStatus;
use App\Domain\Shopify\Enums\JobType;
use App\Domain\Shopify\Events\JobUpdatedEvent;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Shopify\Models\Job
 *
 * @property string $id
 * @property string $store_id
 * @property string $shopify_job_id
 * @property string $query
 * @property JobType $type
 * @property string $domain
 * @property string $topic
 * @property string|null $export_file_url
 * @property string|null $export_partial_file_url
 * @property JobStatus $status
 * @property JobErrorCode|null $error_code
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @method static Builder|Job newModelQuery()
 * @method static Builder|Job newQuery()
 * @method static Builder|Job onlyTrashed()
 * @method static Builder|Job query()
 * @method static Builder|Job whereCreatedAt($value)
 * @method static Builder|Job whereDeletedAt($value)
 * @method static Builder|Job whereDomain($value)
 * @method static Builder|Job whereErrorCode($value)
 * @method static Builder|Job whereExportFileUrl($value)
 * @method static Builder|Job whereExportPartialFileUrl($value)
 * @method static Builder|Job whereId($value)
 * @method static Builder|Job whereQuery($value)
 * @method static Builder|Job whereShopifyJobId($value)
 * @method static Builder|Job whereStatus($value)
 * @method static Builder|Job whereStoreId($value)
 * @method static Builder|Job whereTopic($value)
 * @method static Builder|Job whereType($value)
 * @method static Builder|Job whereUpdatedAt($value)
 * @method static Builder|Job withTrashed()
 * @method static Builder|Job withoutCurrentStore()
 * @method static Builder|Job withoutTrashed()
 * @mixin Eloquent
 */
class Job extends Model
{
    use HasUuids;
    use HasModelFactory;
    use SoftDeletes;
    use CurrentStore;
    use HasTimestamps;

    protected $table = 'shopify_jobs';

    protected $fillable = [
        'store_id',
        'query',
        'type',
        'domain',
        'topic',
        'shopify_job_id',
        'export_file_url',
        'export_partial_file_url',
        'status',
        'error_code',
    ];

    protected $dispatchesEvents = [
        'updated' => JobUpdatedEvent::class,
    ];

    protected function casts(): array
    {
        return [
            'status' => JobStatus::class,
            'type' => JobType::class,
            'error_code' => JobErrorCode::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
