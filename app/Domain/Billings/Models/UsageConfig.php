<?php
declare(strict_types=1);

namespace App\Domain\Billings\Models;

use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Billings\Models\UsageConfig
 *
 * @property string $id
 * @property string $store_id
 * @property string $subscription_line_item_id
 * @property string $description
 * @property array $config
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property CurrencyAlpha3 $currency
 * @property CarbonImmutable $valid_from
 * @property CarbonImmutable|null $valid_to
 * @method static Builder|UsageConfig newModelQuery()
 * @method static Builder|UsageConfig newQuery()
 * @method static Builder|UsageConfig onlyTrashed()
 * @method static Builder|UsageConfig query()
 * @method static Builder|UsageConfig whereConfig($value)
 * @method static Builder|UsageConfig whereCreatedAt($value)
 * @method static Builder|UsageConfig whereCurrency($value)
 * @method static Builder|UsageConfig whereDeletedAt($value)
 * @method static Builder|UsageConfig whereDescription($value)
 * @method static Builder|UsageConfig whereId($value)
 * @method static Builder|UsageConfig whereStoreId($value)
 * @method static Builder|UsageConfig whereSubscriptionLineItemId($value)
 * @method static Builder|UsageConfig whereUpdatedAt($value)
 * @method static Builder|UsageConfig whereValidFrom($value)
 * @method static Builder|UsageConfig whereValidTo($value)
 * @method static Builder|UsageConfig withTrashed()
 * @method static Builder|UsageConfig withoutCurrentStore()
 * @method static Builder|UsageConfig withoutTrashed()
 * @mixin Eloquent
 */
class UsageConfig extends Model
{
    use HasModelFactory;
    use SoftDeletes;
    use HasUuids;
    use CurrentStore;

    protected $fillable = [
        'store_id',
        'subscription_line_item_id',
        'description',
        'config',
        'currency',
        'valid_from',
        'valid_to',
    ];

    protected $table = 'billings_app_usage_configs';

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'currency' => CurrencyAlpha3::class,
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
        ];
    }
}
