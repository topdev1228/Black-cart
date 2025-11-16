<?php
declare(strict_types=1);

namespace App\Domain\Orders\Models;

use App\Domain\Shared\Models\Casts\Money as MoneyCast;
use App\Domain\Shared\Traits\HasModelFactory;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Orders\Models\ReturnLineItem
 *
 * @property string $id
 * @property string $source_id
 * @property string $order_return_id
 * @property string $source_return_id
 * @property string $line_item_id
 * @property string|null $customer_note
 * @property int $quantity
 * @property string|null $return_reason
 * @property string|null $return_reason_note
 * @property string $shop_currency
 * @property string $customer_currency
 * @property \Brick\Money\Money $gross_sales_shop_amount
 * @property \Brick\Money\Money $gross_sales_customer_amount
 * @property \Brick\Money\Money $discounts_shop_amount
 * @property \Brick\Money\Money $discounts_customer_amount
 * @property \Brick\Money\Money $tax_customer_amount
 * @property \Brick\Money\Money $tax_shop_amount
 * @property array $return_line_item_data
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property bool $is_tbyb
 * @property-read \App\Domain\Orders\Models\OrderReturn|null $orderReturn
 * @method static Builder|ReturnLineItem newModelQuery()
 * @method static Builder|ReturnLineItem newQuery()
 * @method static Builder|ReturnLineItem onlyTrashed()
 * @method static Builder|ReturnLineItem query()
 * @method static Builder|ReturnLineItem whereCreatedAt($value)
 * @method static Builder|ReturnLineItem whereCustomerCurrency($value)
 * @method static Builder|ReturnLineItem whereCustomerNote($value)
 * @method static Builder|ReturnLineItem whereDeletedAt($value)
 * @method static Builder|ReturnLineItem whereDiscountsCustomerAmount($value)
 * @method static Builder|ReturnLineItem whereDiscountsShopAmount($value)
 * @method static Builder|ReturnLineItem whereGrossSalesCustomerAmount($value)
 * @method static Builder|ReturnLineItem whereGrossSalesShopAmount($value)
 * @method static Builder|ReturnLineItem whereId($value)
 * @method static Builder|ReturnLineItem whereIsTbyb($value)
 * @method static Builder|ReturnLineItem whereLineItemId($value)
 * @method static Builder|ReturnLineItem whereOrderReturnId($value)
 * @method static Builder|ReturnLineItem whereQuantity($value)
 * @method static Builder|ReturnLineItem whereReturnLineItemData($value)
 * @method static Builder|ReturnLineItem whereReturnReason($value)
 * @method static Builder|ReturnLineItem whereReturnReasonNote($value)
 * @method static Builder|ReturnLineItem whereShopCurrency($value)
 * @method static Builder|ReturnLineItem whereSourceId($value)
 * @method static Builder|ReturnLineItem whereSourceReturnId($value)
 * @method static Builder|ReturnLineItem whereTaxCustomerAmount($value)
 * @method static Builder|ReturnLineItem whereTaxShopAmount($value)
 * @method static Builder|ReturnLineItem whereUpdatedAt($value)
 * @method static Builder|ReturnLineItem withTrashed()
 * @method static Builder|ReturnLineItem withoutTrashed()
 * @mixin Eloquent
 */
class ReturnLineItem extends Model
{
    use HasModelFactory;
    use HasUuids;
    use SoftDeletes;

    protected $table = 'orders_returns_line_items';

    protected $fillable = [
        'source_id',
        'order_return_id',
        'source_order_id',
        'source_return_id',
        'line_item_id',
        'customer_note',
        'quantity',
        'return_reason',
        'return_reason_note',
        'shop_currency',
        'customer_currency',
        'gross_sales_shop_amount',
        'gross_sales_customer_amount',
        'discounts_shop_amount',
        'discounts_customer_amount',
        'tax_shop_amount',
        'tax_customer_amount',
        'return_line_item_data',
        'is_tbyb',
    ];

    protected function casts(): array
    {
        return [
            'return_line_item_data' => 'array',
            'gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'tax_shop_amount' => MoneyCast::class . ':shop_currency',
            'tax_customer_amount' => MoneyCast::class . ':customer_currency',
            'is_tbyb' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }
}
