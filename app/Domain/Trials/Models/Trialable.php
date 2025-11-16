<?php
declare(strict_types=1);

namespace App\Domain\Trials\Models;

use App\Domain\Shared\Traits\HasModelFactory;
use App\Domain\Trials\Enums\TrialStatus;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Trials\Models\Trialable
 *
 * @property string $id
 * @property string $source_key
 * @property string $source_id
 * @property string|null $group_key
 * @property TrialStatus $status
 * @property int $trial_duration
 * @property CarbonImmutable|null $expires_at
 * @property string|null $title
 * @property string|null $subtitle
 * @property string|null $image
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @method static Builder|Trialable newModelQuery()
 * @method static Builder|Trialable newQuery()
 * @method static Builder|Trialable onlyTrashed()
 * @method static Builder|Trialable query()
 * @method static Builder|Trialable whereCreatedAt($value)
 * @method static Builder|Trialable whereDeletedAt($value)
 * @method static Builder|Trialable whereExpiresAt($value)
 * @method static Builder|Trialable whereGroupKey($value)
 * @method static Builder|Trialable whereId($value)
 * @method static Builder|Trialable whereImage($value)
 * @method static Builder|Trialable whereSourceId($value)
 * @method static Builder|Trialable whereSourceKey($value)
 * @method static Builder|Trialable whereStatus($value)
 * @method static Builder|Trialable whereSubtitle($value)
 * @method static Builder|Trialable whereTitle($value)
 * @method static Builder|Trialable whereTrialDuration($value)
 * @method static Builder|Trialable whereUpdatedAt($value)
 * @method static Builder|Trialable withTrashed()
 * @method static Builder|Trialable withoutTrashed()
 * @mixin Eloquent
 */
class Trialable extends Model
{
    use HasModelFactory;
    use HasUuids;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'source_id',
        'source_key',
        'group_key',
        'status',
        'trial_duration',
        'expires_at',
        'title',
        'subtitle',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'status' => TrialStatus::class,
            'expires_at' => 'datetime',
        ];
    }
}
