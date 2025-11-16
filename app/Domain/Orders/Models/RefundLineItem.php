<?php
declare(strict_types=1);

namespace App\Domain\Orders\Models;

use App\Domain\Shared\Models\Casts\Money as MoneyCast;
use App\Domain\Shared\Traits\HasModelFactory;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Orders\Models\RefundLineItem
 *
 * @property string $id
 * @property string $source_refund_reference_id
 * @property string $line_item_id
 * @property int $quantity
 * @property CurrencyAlpha3 $shop_currency
 * @property CurrencyAlpha3 $customer_currency
 * @property \Brick\Money\Money $gross_sales_shop_amount
 * @property \Brick\Money\Money $gross_sales_customer_amount
 * @property \Brick\Money\Money $discounts_shop_amount
 * @property \Brick\Money\Money $discounts_customer_amount
 * @property bool $is_tbyb
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property \Brick\Money\Money $tax_shop_amount
 * @property \Brick\Money\Money $tax_customer_amount
 * @property \Brick\Money\Money $total_shop_amount
 * @property \Brick\Money\Money $total_customer_amount
 * @property string $refund_id
 * @property \Brick\Money\Money $deposit_shop_amount
 * @property \Brick\Money\Money $deposit_customer_amount
 * @property-read \App\Domain\Orders\Models\Refund|null $refund
 * @method static Builder|RefundLineItem newModelQuery()
 * @method static Builder|RefundLineItem newQuery()
 * @method static Builder|RefundLineItem query()
 * @method static Builder|RefundLineItem whereCreatedAt($value)
 * @method static Builder|RefundLineItem whereCustomerCurrency($value)
 * @method static Builder|RefundLineItem whereDepositCustomerAmount($value)
 * @method static Builder|RefundLineItem whereDepositShopAmount($value)
 * @method static Builder|RefundLineItem whereDiscountsCustomerAmount($value)
 * @method static Builder|RefundLineItem whereDiscountsShopAmount($value)
 * @method static Builder|RefundLineItem whereGrossSalesCustomerAmount($value)
 * @method static Builder|RefundLineItem whereGrossSalesShopAmount($value)
 * @method static Builder|RefundLineItem whereId($value)
 * @method static Builder|RefundLineItem whereIsTbyb($value)
 * @method static Builder|RefundLineItem whereLineItemId($value)
 * @method static Builder|RefundLineItem whereQuantity($value)
 * @method static Builder|RefundLineItem whereRefundId($value)
 * @method static Builder|RefundLineItem whereShopCurrency($value)
 * @method static Builder|RefundLineItem whereSourceRefundReferenceId($value)
 * @method static Builder|RefundLineItem whereTaxCustomerAmount($value)
 * @method static Builder|RefundLineItem whereTaxShopAmount($value)
 * @method static Builder|RefundLineItem whereTotalCustomerAmount($value)
 * @method static Builder|RefundLineItem whereTotalShopAmount($value)
 * @method static Builder|RefundLineItem whereUpdatedAt($value)
 * @mixin Eloquent
 */
class RefundLineItem extends Model
{
    use HasModelFactory;
    use HasUuids;
    use HasTimestamps;

    protected $table = 'orders_refund_line_items';

    protected $fillable = [
        'refund_id',
        'source_refund_reference_id',
        'line_item_id',
        'quantity',
        'shop_currency',
        'customer_currency',
        'gross_sales_shop_amount',
        'gross_sales_customer_amount',
        'deposit_shop_amount',
        'deposit_customer_amount',
        'discounts_shop_amount',
        'discounts_customer_amount',
        'tax_shop_amount',
        'tax_customer_amount',
        'total_shop_amount',
        'total_customer_amount',
        'is_tbyb',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'shop_currency' => CurrencyAlpha3::class,
            'customer_currency' => CurrencyAlpha3::class,
            'is_tbyb' => 'boolean',
            'deposit_customer_amount' => MoneyCast::class . ':customer_currency',
            'deposit_shop_amount' => MoneyCast::class . ':shop_currency',
            'discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'tax_customer_amount' => MoneyCast::class . ':customer_currency',
            'tax_shop_amount' => MoneyCast::class . ':shop_currency',
            'total_customer_amount' => MoneyCast::class . ':customer_currency',
            'total_shop_amount' => MoneyCast::class . ':shop_currency',
        ];
    }

    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class);
    }
}
