<?php
declare(strict_types=1);

namespace App\Domain\Orders\Models;

use App\Domain\Orders\Enums\DepositType;
use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\LineItemStatusUpdatedBy;
use App\Domain\Orders\Events\LineItemSavedEvent;
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
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Orders\Models\LineItem
 *
 * @property string $id
 * @property string $source_id
 * @property string|null $source_product_id
 * @property string|null $source_variant_id
 * @property string|null $product_title
 * @property string|null $variant_title
 * @property string|null $thumbnail
 * @property string $order_id
 * @property string|null $source_order_id
 * @property int $quantity
 * @property int $original_quantity
 * @property LineItemStatus $status
 * @property string|null $trialable_id
 * @property string|null $trial_group_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property bool $is_tbyb
 * @property string|null $selling_plan_id
 * @property DepositType|null $deposit_type
 * @property int|null $deposit_value
 * @property \Brick\Money\Money|null $deposit_shop_amount
 * @property \Brick\Money\Money|null $deposit_customer_amount
 * @property CurrencyAlpha3 $shop_currency
 * @property CurrencyAlpha3 $customer_currency
 * @property \Brick\Money\Money $price_shop_amount
 * @property \Brick\Money\Money $price_customer_amount
 * @property \Brick\Money\Money $total_price_shop_amount
 * @property \Brick\Money\Money $total_price_customer_amount
 * @property \Brick\Money\Money $discount_shop_amount
 * @property \Brick\Money\Money $discount_customer_amount
 * @property \Brick\Money\Money $tax_shop_amount
 * @property \Brick\Money\Money $tax_customer_amount
 * @property LineItemDecisionStatus $decision_status
 * @property LineItemStatusUpdatedBy $status_updated_by
 * @property-read \App\Domain\Orders\Models\Order|null $order
 * @method static Builder|LineItem newModelQuery()
 * @method static Builder|LineItem newQuery()
 * @method static Builder|LineItem onlyTrashed()
 * @method static Builder|LineItem query()
 * @method static Builder|LineItem whereCreatedAt($value)
 * @method static Builder|LineItem whereCustomerCurrency($value)
 * @method static Builder|LineItem whereDecisionStatus($value)
 * @method static Builder|LineItem whereDeletedAt($value)
 * @method static Builder|LineItem whereDepositCustomerAmount($value)
 * @method static Builder|LineItem whereDepositShopAmount($value)
 * @method static Builder|LineItem whereDepositType($value)
 * @method static Builder|LineItem whereDepositValue($value)
 * @method static Builder|LineItem whereDiscountCustomerAmount($value)
 * @method static Builder|LineItem whereDiscountShopAmount($value)
 * @method static Builder|LineItem whereId($value)
 * @method static Builder|LineItem whereIsTbyb($value)
 * @method static Builder|LineItem whereOrderId($value)
 * @method static Builder|LineItem whereOriginalQuantity($value)
 * @method static Builder|LineItem wherePriceCustomerAmount($value)
 * @method static Builder|LineItem wherePriceShopAmount($value)
 * @method static Builder|LineItem whereProductTitle($value)
 * @method static Builder|LineItem whereQuantity($value)
 * @method static Builder|LineItem whereSellingPlanId($value)
 * @method static Builder|LineItem whereShopCurrency($value)
 * @method static Builder|LineItem whereSourceId($value)
 * @method static Builder|LineItem whereSourceOrderId($value)
 * @method static Builder|LineItem whereSourceProductId($value)
 * @method static Builder|LineItem whereSourceVariantId($value)
 * @method static Builder|LineItem whereStatus($value)
 * @method static Builder|LineItem whereStatusUpdatedBy($value)
 * @method static Builder|LineItem whereTaxCustomerAmount($value)
 * @method static Builder|LineItem whereTaxShopAmount($value)
 * @method static Builder|LineItem whereThumbnail($value)
 * @method static Builder|LineItem whereTotalPriceCustomerAmount($value)
 * @method static Builder|LineItem whereTotalPriceShopAmount($value)
 * @method static Builder|LineItem whereTrialGroupId($value)
 * @method static Builder|LineItem whereTrialableId($value)
 * @method static Builder|LineItem whereUpdatedAt($value)
 * @method static Builder|LineItem whereVariantTitle($value)
 * @method static Builder|LineItem withTrashed()
 * @method static Builder|LineItem withoutTrashed()
 * @mixin Eloquent
 */
class LineItem extends Model
{
    use HasModelFactory;
    use HasUuids;
    use SoftDeletes;

    protected $table = 'orders_line_items';

    protected $fillable = [
        'source_id',
        'source_product_id',
        'source_variant_id',
        'product_title',
        'variant_title',
        'thumbnail',
        'order_id',
        'source_order_id',
        'quantity',
        'original_quantity',
        'status',
        'decision_status',
        'trialable_id',
        'trial_group_id',
        'is_tbyb',
        'selling_plan_id',
        'deposit_type',
        'deposit_value',
        'shop_currency',
        'customer_currency',
        'price_shop_amount',
        'price_customer_amount',
        'total_price_shop_amount',
        'total_price_customer_amount',
        'discount_shop_amount',
        'discount_customer_amount',
        'tax_shop_amount',
        'tax_customer_amount',
        'deposit_shop_amount',
        'deposit_customer_amount',
        'status_updated_by',
    ];

    protected $dispatchesEvents = [
        'saved' => LineItemSavedEvent::class,
    ];

    protected function casts(): array
    {
        return [
            'status' => LineItemStatus::class,
            'decision_status' => LineItemDecisionStatus::class,
            'is_tbyb' => 'bool',
            'deposit_type' => DepositType::class,
            'shop_currency' => CurrencyAlpha3::class,
            'customer_currency' => CurrencyAlpha3::class,
            'price_shop_amount' => MoneyCast::class . ':shop_currency',
            'price_customer_amount' => MoneyCast::class . ':customer_currency',
            'total_price_shop_amount' => MoneyCast::class . ':shop_currency',
            'total_price_customer_amount' => MoneyCast::class . ':customer_currency',
            'discount_shop_amount' => MoneyCast::class . ':shop_currency',
            'discount_customer_amount' => MoneyCast::class . ':customer_currency',
            'tax_shop_amount' => MoneyCast::class . ':shop_currency',
            'tax_customer_amount' => MoneyCast::class . ':customer_currency',
            'deposit_shop_amount' => MoneyCast::class . ':shop_currency',
            'deposit_customer_amount' => MoneyCast::class . ':customer_currency',
            'status_updated_by' => LineItemStatusUpdatedBy::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
