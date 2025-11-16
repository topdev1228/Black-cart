<?php
declare(strict_types=1);

namespace App\Domain\Orders\Models;

use App\Domain\Orders\Enums\ReturnStatus;
use App\Domain\Shared\Models\Casts\Money as MoneyCast;
use App\Domain\Shared\Traits\HasModelFactory;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Orders\Models\OrderReturn
 *
 * @property string $id
 * @property string $source_id
 * @property string $order_id
 * @property string $store_id
 * @property string $source_order_id
 * @property string $shop_currency
 * @property string $customer_currency
 * @property string|null $name
 * @property ReturnStatus $status
 * @property int $total_quantity
 * @property \Brick\Money\Money $tbyb_gross_sales_shop_amount
 * @property \Brick\Money\Money $tbyb_gross_sales_customer_amount
 * @property \Brick\Money\Money $tbyb_discounts_shop_amount
 * @property \Brick\Money\Money $tbyb_discounts_customer_amount
 * @property \Brick\Money\Money $upfront_gross_sales_shop_amount
 * @property \Brick\Money\Money $upfront_gross_sales_customer_amount
 * @property \Brick\Money\Money $upfront_discounts_shop_amount
 * @property \Brick\Money\Money $upfront_discounts_customer_amount
 * @property int $tbyb_tax_shop_amount
 * @property int $tbyb_tax_customer_amount
 * @property \Brick\Money\Money $upfront_tax_shop_amount
 * @property \Brick\Money\Money $upfront_tax_customer_amount
 * @property int $tbyb_total_shop_amount
 * @property int $tbyb_total_customer_amount
 * @property array $return_data
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property \Brick\Money\Money $order_return_shop_amount
 * @property \Brick\Money\Money $order_return_customer_amount
 * @property \Brick\Money\Money $tbyb_order_return_total_shop_amount
 * @property \Brick\Money\Money $tbyb_order_return_total_customer_amount
 * @property \Brick\Money\Money $tbyb_return_shop_tax_amount
 * @property \Brick\Money\Money $tbyb_return_customer_tax_amount
 * @property-read Collection<int, \App\Domain\Orders\Models\ReturnLineItem> $lineItems
 * @property-read int|null $line_items_count
 * @property-read \App\Domain\Orders\Models\Order|null $order
 * @method static Builder|OrderReturn newModelQuery()
 * @method static Builder|OrderReturn newQuery()
 * @method static Builder|OrderReturn onlyTrashed()
 * @method static Builder|OrderReturn query()
 * @method static Builder|OrderReturn whereCreatedAt($value)
 * @method static Builder|OrderReturn whereCustomerCurrency($value)
 * @method static Builder|OrderReturn whereDeletedAt($value)
 * @method static Builder|OrderReturn whereId($value)
 * @method static Builder|OrderReturn whereName($value)
 * @method static Builder|OrderReturn whereOrderId($value)
 * @method static Builder|OrderReturn whereReturnData($value)
 * @method static Builder|OrderReturn whereShopCurrency($value)
 * @method static Builder|OrderReturn whereSourceId($value)
 * @method static Builder|OrderReturn whereSourceOrderId($value)
 * @method static Builder|OrderReturn whereStatus($value)
 * @method static Builder|OrderReturn whereStoreId($value)
 * @method static Builder|OrderReturn whereTbybDiscountsCustomerAmount($value)
 * @method static Builder|OrderReturn whereTbybDiscountsShopAmount($value)
 * @method static Builder|OrderReturn whereTbybGrossSalesCustomerAmount($value)
 * @method static Builder|OrderReturn whereTbybGrossSalesShopAmount($value)
 * @method static Builder|OrderReturn whereTbybTaxCustomerAmount($value)
 * @method static Builder|OrderReturn whereTbybTaxShopAmount($value)
 * @method static Builder|OrderReturn whereTbybTotalCustomerAmount($value)
 * @method static Builder|OrderReturn whereTbybTotalShopAmount($value)
 * @method static Builder|OrderReturn whereTotalQuantity($value)
 * @method static Builder|OrderReturn whereUpdatedAt($value)
 * @method static Builder|OrderReturn whereUpfrontDiscountsCustomerAmount($value)
 * @method static Builder|OrderReturn whereUpfrontDiscountsShopAmount($value)
 * @method static Builder|OrderReturn whereUpfrontGrossSalesCustomerAmount($value)
 * @method static Builder|OrderReturn whereUpfrontGrossSalesShopAmount($value)
 * @method static Builder|OrderReturn whereUpfrontTaxCustomerAmount($value)
 * @method static Builder|OrderReturn whereUpfrontTaxShopAmount($value)
 * @method static Builder|OrderReturn withTrashed()
 * @method static Builder|OrderReturn withoutTrashed()
 * @mixin Eloquent
 */
class OrderReturn extends Model
{
    use HasModelFactory;
    use HasUuids;
    use SoftDeletes;

    protected $table = 'orders_returns';

    protected $fillable = [
        'source_id',
        'order_id',
        'store_id',
        'shop_currency',
        'customer_currency',
        'source_order_id',
        'name',
        'status',
        'total_quantity',
        'tbyb_gross_sales_shop_amount',
        'tbyb_gross_sales_customer_amount',
        'tbyb_discounts_shop_amount',
        'tbyb_discounts_customer_amount',
        'upfront_gross_sales_shop_amount',
        'upfront_gross_sales_customer_amount',
        'upfront_discounts_shop_amount',
        'upfront_discounts_customer_amount',
        'tbyb_total_shop_amount',
        'tbyb_total_customer_amount',
        'tbyb_tax_shop_amount',
        'tbyb_tax_customer_amount',
        'upfront_tax_shop_amount',
        'upfront_tax_customer_amount',
        'return_data',
    ];

    protected function casts(): array
    {
        return [
            'return_data' => 'array',
            'status' => ReturnStatus::class,
            'tbyb_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'upfront_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'upfront_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'order_return_shop_amount' => MoneyCast::class . ':shop_currency',
            'order_return_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_order_return_total_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_order_return_total_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_return_shop_tax_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_return_customer_tax_amount' => MoneyCast::class . ':customer_currency',
            'upfront_tax_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_tax_customer_amount' => MoneyCast::class . ':customer_currency',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(ReturnLineItem::class);
    }
}
