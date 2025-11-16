<?php
declare(strict_types=1);

namespace App\Domain\Billings\Models;

use App\Domain\Billings\Events\TbybNetSaleCreatedEvent;
use App\Domain\Shared\Models\Casts\Money;
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
 * App\Domain\Billings\Models\TbybNetSale
 *
 * @property string $id
 * @property string $store_id
 * @property CarbonImmutable $date_start
 * @property CarbonImmutable $date_end
 * @property CarbonImmutable $time_range_start
 * @property CarbonImmutable $time_range_end
 * @property CurrencyAlpha3 $currency
 * @property \Brick\Money\Money $tbyb_gross_sales
 * @property \Brick\Money\Money $tbyb_discounts
 * @property \Brick\Money\Money $tbyb_refunded_gross_sales
 * @property \Brick\Money\Money $tbyb_refunded_discounts
 * @property \Brick\Money\Money $tbyb_net_sales
 * @property bool $is_first_of_billing_period
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @method static Builder|TbybNetSale newModelQuery()
 * @method static Builder|TbybNetSale newQuery()
 * @method static Builder|TbybNetSale onlyTrashed()
 * @method static Builder|TbybNetSale query()
 * @method static Builder|TbybNetSale whereCreatedAt($value)
 * @method static Builder|TbybNetSale whereCurrency($value)
 * @method static Builder|TbybNetSale whereDateEnd($value)
 * @method static Builder|TbybNetSale whereDateStart($value)
 * @method static Builder|TbybNetSale whereDeletedAt($value)
 * @method static Builder|TbybNetSale whereId($value)
 * @method static Builder|TbybNetSale whereIsFirstOfBillingPeriod($value)
 * @method static Builder|TbybNetSale whereStoreId($value)
 * @method static Builder|TbybNetSale whereTbybDiscounts($value)
 * @method static Builder|TbybNetSale whereTbybGrossSales($value)
 * @method static Builder|TbybNetSale whereTbybNetSales($value)
 * @method static Builder|TbybNetSale whereTbybRefundedDiscounts($value)
 * @method static Builder|TbybNetSale whereTbybRefundedGrossSales($value)
 * @method static Builder|TbybNetSale whereTimeRangeEnd($value)
 * @method static Builder|TbybNetSale whereTimeRangeStart($value)
 * @method static Builder|TbybNetSale whereUpdatedAt($value)
 * @method static Builder|TbybNetSale withTrashed()
 * @method static Builder|TbybNetSale withoutCurrentStore()
 * @method static Builder|TbybNetSale withoutTrashed()
 * @mixin Eloquent
 */
class TbybNetSale extends Model
{
    use HasModelFactory;
    use SoftDeletes;
    use HasUuids;
    use CurrentStore;

    protected $table = 'billings_tbyb_net_sales';

    protected $fillable = [
        'store_id',
        'date_start',
        'date_end',
        'time_range_start',
        'time_range_end',
        'currency',
        'tbyb_gross_sales',
        'tbyb_discounts',
        'tbyb_refunded_gross_sales',
        'tbyb_refunded_discounts',
        'tbyb_net_sales',
        'is_first_of_billing_period',
    ];

    protected $dispatchesEvents = [
        'created' => TbybNetSaleCreatedEvent::class,
    ];

    protected function casts(): array
    {
        return [
            'currency' => CurrencyAlpha3::class,
            'tbyb_gross_sales' => Money::class . ':currency',
            'tbyb_discounts' => Money::class . ':currency',
            'tbyb_refunded_gross_sales' => Money::class . ':currency',
            'tbyb_refunded_discounts' => Money::class . ':currency',
            'tbyb_net_sales' => Money::class . ':currency',
            'date_start' => 'datetime',
            'date_end' => 'datetime',
            'time_range_start' => 'datetime',
            'time_range_end' => 'datetime',
            'is_first_of_billing_period' => 'boolean',
        ];
    }
}
