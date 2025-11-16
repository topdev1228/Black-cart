<?php
declare(strict_types=1);

namespace App\Domain\Orders\Models;

use App\Domain\Orders\Events\RefundCreatedEvent;
use App\Domain\Shared\Models\Casts\Money as MoneyCast;
use App\Domain\Shared\Traits\CurrentStore;
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
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Orders\Models\Refund
 *
 * @property string $id
 * @property string|null $source_refund_reference_id
 * @property string $order_id
 * @property CurrencyAlpha3 $shop_currency
 * @property CurrencyAlpha3 $customer_currency
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property Money $tbyb_gross_sales_shop_amount
 * @property Money $tbyb_gross_sales_customer_amount
 * @property Money $tbyb_discounts_shop_amount
 * @property Money $tbyb_discounts_customer_amount
 * @property Money $upfront_gross_sales_shop_amount
 * @property Money $upfront_gross_sales_customer_amount
 * @property Money $upfront_discounts_shop_amount
 * @property Money $upfront_discounts_customer_amount
 * @property Money $order_level_refund_shop_amount
 * @property Money $order_level_refund_customer_amount
 * @property string|null $store_id
 * @property Money|null $tbyb_total_shop_amount
 * @property Money|null $tbyb_total_customer_amount
 * @property Money|null $upfront_total_shop_amount
 * @property Money|null $upfront_total_customer_amount
 * @property Money $tbyb_deposit_shop_amount
 * @property Money $tbyb_deposit_customer_amount
 * @property Money $refunded_shop_amount
 * @property Money $refunded_customer_amount
 * @property array|null $refund_data
 * @property-read Collection<int, \App\Domain\Orders\Models\RefundLineItem> $lineItems
 * @property-read int|null $line_items_count
 * @property-read Order|null $order
 * @method static Builder|Refund newModelQuery()
 * @method static Builder|Refund newQuery()
 * @method static Builder|Refund query()
 * @method static Builder|Refund whereCreatedAt($value)
 * @method static Builder|Refund whereCustomerCurrency($value)
 * @method static Builder|Refund whereId($value)
 * @method static Builder|Refund whereOrderId($value)
 * @method static Builder|Refund whereOrderLevelRefundCustomerAmount($value)
 * @method static Builder|Refund whereOrderLevelRefundShopAmount($value)
 * @method static Builder|Refund whereRefundData($value)
 * @method static Builder|Refund whereRefundedCustomerAmount($value)
 * @method static Builder|Refund whereRefundedShopAmount($value)
 * @method static Builder|Refund whereShopCurrency($value)
 * @method static Builder|Refund whereSourceRefundReferenceId($value)
 * @method static Builder|Refund whereStoreId($value)
 * @method static Builder|Refund whereTbybDepositCustomerAmount($value)
 * @method static Builder|Refund whereTbybDepositShopAmount($value)
 * @method static Builder|Refund whereTbybDiscountsCustomerAmount($value)
 * @method static Builder|Refund whereTbybDiscountsShopAmount($value)
 * @method static Builder|Refund whereTbybGrossSalesCustomerAmount($value)
 * @method static Builder|Refund whereTbybGrossSalesShopAmount($value)
 * @method static Builder|Refund whereTbybTotalCustomerAmount($value)
 * @method static Builder|Refund whereTbybTotalShopAmount($value)
 * @method static Builder|Refund whereUpdatedAt($value)
 * @method static Builder|Refund whereUpfrontDiscountsCustomerAmount($value)
 * @method static Builder|Refund whereUpfrontDiscountsShopAmount($value)
 * @method static Builder|Refund whereUpfrontGrossSalesCustomerAmount($value)
 * @method static Builder|Refund whereUpfrontGrossSalesShopAmount($value)
 * @method static Builder|Refund whereUpfrontTotalCustomerAmount($value)
 * @method static Builder|Refund whereUpfrontTotalShopAmount($value)
 * @method static Builder|Refund withoutCurrentStore()
 * @mixin Eloquent
 */
class Refund extends Model
{
    use HasModelFactory;
    use HasUuids;
    use CurrentStore;

    protected $table = 'orders_refunds';

    protected $fillable = [
        'id',
        'source_refund_reference_id',
        'store_id',
        'order_id',
        'shop_currency',
        'customer_currency',
        'refunded_shop_amount',
        'refunded_customer_amount',
        'tbyb_gross_sales_shop_amount',
        'tbyb_gross_sales_customer_amount',
        'tbyb_deposit_shop_amount',
        'tbyb_deposit_customer_amount',
        'tbyb_discounts_shop_amount',
        'tbyb_discounts_customer_amount',
        'tbyb_total_shop_amount',
        'tbyb_total_customer_amount',
        'upfront_gross_sales_shop_amount',
        'upfront_gross_sales_customer_amount',
        'upfront_discounts_shop_amount',
        'upfront_discounts_customer_amount',
        'upfront_total_shop_amount',
        'upfront_total_customer_amount',
        'order_level_refund_shop_amount',
        'order_level_refund_customer_amount',
        'refund_data',
    ];

    protected $dispatchesEvents = [
        'created' => RefundCreatedEvent::class,
    ];

    protected function casts(): array
    {
        return [
            'shop_currency' => CurrencyAlpha3::class,
            'customer_currency' => CurrencyAlpha3::class,
            'refunded_shop_amount' => MoneyCast::class . ':shop_currency',
            'refunded_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_deposit_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_deposit_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_total_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_total_customer_amount' => MoneyCast::class . ':customer_currency',
            'upfront_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'upfront_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'upfront_total_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_total_customer_amount' => MoneyCast::class . ':customer_currency',
            'order_level_refund_shop_amount' => MoneyCast::class . ':shop_currency',
            'order_level_refund_customer_amount' => MoneyCast::class . ':customer_currency',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'refund_data' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(RefundLineItem::class);
    }
}
