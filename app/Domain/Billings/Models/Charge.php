<?php
declare(strict_types=1);

namespace App\Domain\Billings\Models;

use App\Domain\Shared\Models\Casts\Money as MoneyCast;
use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Billings\Models\Charge
 *
 * @property string $id
 * @property string $store_id
 * @property string|null $tbyb_net_sale_id
 * @property CurrencyAlpha3 $currency
 * @property \Brick\Money\Money $amount
 * @property \Brick\Money\Money $balance
 * @property bool $is_billed
 * @property CarbonImmutable|null $billed_at
 * @property CarbonImmutable|null $time_range_start
 * @property CarbonImmutable|null $time_range_end
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property \Brick\Money\Money|null $step_size
 * @property \Brick\Money\Money|null $step_start_amount
 * @property \Brick\Money\Money|null $step_end_amount
 * @property bool $is_first_of_billing_period
 * @method static Builder|Charge newModelQuery()
 * @method static Builder|Charge newQuery()
 * @method static Builder|Charge query()
 * @method static Builder|Charge whereAmount($value)
 * @method static Builder|Charge whereBalance($value)
 * @method static Builder|Charge whereBilledAt($value)
 * @method static Builder|Charge whereCreatedAt($value)
 * @method static Builder|Charge whereCurrency($value)
 * @method static Builder|Charge whereId($value)
 * @method static Builder|Charge whereIsBilled($value)
 * @method static Builder|Charge whereIsFirstOfBillingPeriod($value)
 * @method static Builder|Charge whereStepEndAmount($value)
 * @method static Builder|Charge whereStepSize($value)
 * @method static Builder|Charge whereStepStartAmount($value)
 * @method static Builder|Charge whereStoreId($value)
 * @method static Builder|Charge whereTbybNetSaleId($value)
 * @method static Builder|Charge whereTimeRangeEnd($value)
 * @method static Builder|Charge whereTimeRangeStart($value)
 * @method static Builder|Charge whereUpdatedAt($value)
 * @method static Builder|Charge withoutCurrentStore()
 * @mixin Eloquent
 */
class Charge extends Model
{
    use HasModelFactory;
    use HasUuids;
    use CurrentStore;

    protected $table = 'billings_charges';

    protected $fillable = [
        'id',
        'store_id',
        'tbyb_net_sale_id',
        'currency',
        'amount',
        'balance',
        'is_billed',
        'billed_at',
        'time_range_start',
        'time_range_end',
        'step_size',
        'step_start_amount',
        'step_end_amount',
        'store_id',
        'is_first_of_billing_period',
    ];

    protected function casts(): array
    {
        return [
            'currency' => CurrencyAlpha3::class,
            'amount' => MoneyCast::class,
            'balance' => MoneyCast::class,
            'is_billed' => 'boolean',
            'billed_at' => 'datetime:Y-m-d\TH:i:sP',
            'time_range_start' => 'datetime:Y-m-d\TH:i:sP',
            'time_range_end' => 'datetime:Y-m-d\TH:i:sP',
            'created_at' => 'datetime:Y-m-d\TH:i:sP',
            'updated_at' => 'datetime:Y-m-d\TH:i:sP',
            'step_size' => MoneyCast::class,
            'step_start_amount' => MoneyCast::class,
            'step_end_amount' => MoneyCast::class,
            'is_first_of_billing_period' => 'boolean',
        ];
    }
}
