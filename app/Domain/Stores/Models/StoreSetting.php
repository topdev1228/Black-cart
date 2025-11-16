<?php
declare(strict_types=1);

namespace App\Domain\Stores\Models;

use App\Domain\Shared\Models\Casts\OptionalEncrypt;
use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelCollection;
use App\Domain\Shared\Traits\HasModelFactory;
use App\Domain\Shared\Traits\OptionalSecure;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Stores\Models\StoreSetting
 *
 * @property string $id
 * @property string $name
 * @property mixed $value
 * @property bool $is_secure
 * @property string $store_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read \App\Domain\Stores\Models\Store|null $store
 * @method static Builder|StoreSetting newModelQuery()
 * @method static Builder|StoreSetting newQuery()
 * @method static Builder|StoreSetting onlyTrashed()
 * @method static Builder|StoreSetting query()
 * @method static Builder|StoreSetting whereCreatedAt($value)
 * @method static Builder|StoreSetting whereDeletedAt($value)
 * @method static Builder|StoreSetting whereId($value)
 * @method static Builder|StoreSetting whereIsSecure($value)
 * @method static Builder|StoreSetting whereName($value)
 * @method static Builder|StoreSetting whereStoreId($value)
 * @method static Builder|StoreSetting whereUpdatedAt($value)
 * @method static Builder|StoreSetting whereValue($value)
 * @method static Builder|StoreSetting withSecure()
 * @method static Builder|StoreSetting withTrashed()
 * @method static Builder|StoreSetting withoutCurrentStore()
 * @method static Builder|StoreSetting withoutTrashed()
 * @mixin Eloquent
 */
class StoreSetting extends Model
{
    use HasUuids;
    use HasModelFactory;
    use HasModelCollection;
    use HasTimestamps;
    use OptionalSecure;
    use CurrentStore;
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'is_secure',
        'name',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'is_secure' => 'boolean',
            'value' => OptionalEncrypt::class,
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
