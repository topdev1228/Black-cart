<?php
declare(strict_types=1);

namespace App\Domain\Programs\Models;

use App\Domain\Programs\Enums\DepositType;
use App\Domain\Programs\Events\ProgramSavedEvent;
use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Programs\Models\Program
 *
 * @property string $id
 * @property string $name
 * @property string $store_id
 * @property string|null $shopify_selling_plan_group_id
 * @property string|null $shopify_selling_plan_id
 * @property int $try_period_days
 * @property DepositType $deposit_type Supports fixed or percentage
 * @property int $deposit_value Value in cents if deposit_type = fixed
 * @property CurrencyAlpha3 $currency ISO 4127
 * @property int $min_tbyb_items
 * @property int|null $max_tbyb_items NULL means unlimited
 * @property string|null $product_ids Array of string IDs
 * @property string|null $product_variant_ids Array of string IDs
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property int $drop_off_days Number of extra days to allow a return to be processed after a customer has dropped off their package.
 * @method static Builder|Program newModelQuery()
 * @method static Builder|Program newQuery()
 * @method static Builder|Program onlyTrashed()
 * @method static Builder|Program query()
 * @method static Builder|Program whereCreatedAt($value)
 * @method static Builder|Program whereCurrency($value)
 * @method static Builder|Program whereDeletedAt($value)
 * @method static Builder|Program whereDepositType($value)
 * @method static Builder|Program whereDepositValue($value)
 * @method static Builder|Program whereDropOffDays($value)
 * @method static Builder|Program whereId($value)
 * @method static Builder|Program whereMaxTbybItems($value)
 * @method static Builder|Program whereMinTbybItems($value)
 * @method static Builder|Program whereName($value)
 * @method static Builder|Program whereProductIds($value)
 * @method static Builder|Program whereProductVariantIds($value)
 * @method static Builder|Program whereShopifySellingPlanGroupId($value)
 * @method static Builder|Program whereShopifySellingPlanId($value)
 * @method static Builder|Program whereStoreId($value)
 * @method static Builder|Program whereTryPeriodDays($value)
 * @method static Builder|Program whereUpdatedAt($value)
 * @method static Builder|Program withTrashed()
 * @method static Builder|Program withoutCurrentStore()
 * @method static Builder|Program withoutTrashed()
 * @mixin Eloquent
 */
class Program extends Model
{
    use HasUuids;
    use HasModelFactory;
    use SoftDeletes;
    use CurrentStore;
    use HasTimestamps;

    protected $fillable = [
        'name',
        'store_id',
        'shopify_selling_plan_group_id',
        'shopify_selling_plan_id',
        'try_period_days',
        'deposit_type',
        'deposit_value',
        'currency',
        'min_tbyb_items',
        'max_tbyb_items',
        'drop_off_days',
    ];

    protected $dispatchesEvents = [
        'saved' => ProgramSavedEvent::class,
    ];

    protected function casts(): array
    {
        return [
            'currency' => CurrencyAlpha3::class,
            'deposit_type' => DepositType::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
