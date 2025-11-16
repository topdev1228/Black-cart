<?php
declare(strict_types=1);

namespace App\Domain\Orders\Models;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\OrderCreatedEvent;
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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Orders\Models\Order
 *
 * @property string $id
 * @property string $store_id
 * @property string $source_id
 * @property OrderStatus $status
 * @property array $order_data
 * @property array|null $blackcart_metadata
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property string|null $name
 * @property int $taxes_included
 * @property int $taxes_exempt
 * @property string $tags
 * @property string $discount_codes
 * @property int $test
 * @property string|null $payment_terms_id
 * @property string|null $payment_terms_name
 * @property string|null $payment_terms_type
 * @property CarbonImmutable|null $trial_expires_at
 * @property CurrencyAlpha3 $shop_currency
 * @property CurrencyAlpha3 $customer_currency
 * @property \Brick\Money\Money $total_shop_amount
 * @property \Brick\Money\Money $total_customer_amount
 * @property \Brick\Money\Money $outstanding_shop_amount
 * @property \Brick\Money\Money $outstanding_customer_amount
 * @property \Brick\Money\Money $original_tbyb_gross_sales_shop_amount
 * @property \Brick\Money\Money $original_tbyb_gross_sales_customer_amount
 * @property \Brick\Money\Money $original_upfront_gross_sales_shop_amount
 * @property \Brick\Money\Money $original_upfront_gross_sales_customer_amount
 * @property \Brick\Money\Money $original_total_gross_sales_shop_amount
 * @property \Brick\Money\Money $original_total_gross_sales_customer_amount
 * @property \Brick\Money\Money $original_tbyb_discounts_shop_amount
 * @property \Brick\Money\Money $original_tbyb_discounts_customer_amount
 * @property \Brick\Money\Money $original_upfront_discounts_shop_amount
 * @property \Brick\Money\Money $original_upfront_discounts_customer_amount
 * @property \Brick\Money\Money $original_total_discounts_shop_amount
 * @property \Brick\Money\Money $original_total_discounts_customer_amount
 * @property \Brick\Money\Money $tbyb_refund_gross_sales_shop_amount
 * @property \Brick\Money\Money $tbyb_refund_gross_sales_customer_amount
 * @property \Brick\Money\Money $upfront_refund_gross_sales_shop_amount
 * @property \Brick\Money\Money $upfront_refund_gross_sales_customer_amount
 * @property \Brick\Money\Money $total_order_level_refunds_shop_amount
 * @property \Brick\Money\Money $total_order_level_refunds_customer_amount
 * @property \Brick\Money\Money $tbyb_refund_discounts_shop_amount
 * @property \Brick\Money\Money $tbyb_refund_discounts_customer_amount
 * @property \Brick\Money\Money $upfront_refund_discounts_shop_amount
 * @property \Brick\Money\Money $upfront_refund_discounts_customer_amount
 * @property \Brick\Money\Money $tbyb_net_sales_shop_amount
 * @property \Brick\Money\Money $tbyb_net_sales_customer_amount
 * @property \Brick\Money\Money $upfront_net_sales_shop_amount
 * @property \Brick\Money\Money $upfront_net_sales_customer_amount
 * @property \Brick\Money\Money $total_net_sales_shop_amount
 * @property \Brick\Money\Money $total_net_sales_customer_amount
 * @property CarbonImmutable|null $completed_at
 * @property \Brick\Money\Money $original_outstanding_shop_amount
 * @property \Brick\Money\Money $original_outstanding_customer_amount
 * @property CarbonImmutable|null $assumed_delivery_merchant_email_sent_at
 * @property-read Collection<int, \App\Domain\Orders\Models\LineItem> $lineItems
 * @property-read int|null $line_items_count
 * @property-read Collection<int, \App\Domain\Orders\Models\Refund> $refunds
 * @property-read int|null $refunds_count
 * @property-read Collection<int, \App\Domain\Orders\Models\OrderReturn> $returns
 * @property-read int|null $returns_count
 * @property-read Collection<int, \App\Domain\Orders\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order onlyTrashed()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAssumedDeliveryMerchantEmailSentAt($value)
 * @method static Builder|Order whereBlackcartMetadata($value)
 * @method static Builder|Order whereCompletedAt($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereCustomerCurrency($value)
 * @method static Builder|Order whereDeletedAt($value)
 * @method static Builder|Order whereDiscountCodes($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereName($value)
 * @method static Builder|Order whereOrderData($value)
 * @method static Builder|Order whereOriginalOutstandingCustomerAmount($value)
 * @method static Builder|Order whereOriginalOutstandingShopAmount($value)
 * @method static Builder|Order whereOriginalTbybDiscountsCustomerAmount($value)
 * @method static Builder|Order whereOriginalTbybDiscountsShopAmount($value)
 * @method static Builder|Order whereOriginalTbybGrossSalesCustomerAmount($value)
 * @method static Builder|Order whereOriginalTbybGrossSalesShopAmount($value)
 * @method static Builder|Order whereOriginalTotalDiscountsCustomerAmount($value)
 * @method static Builder|Order whereOriginalTotalDiscountsShopAmount($value)
 * @method static Builder|Order whereOriginalTotalGrossSalesCustomerAmount($value)
 * @method static Builder|Order whereOriginalTotalGrossSalesShopAmount($value)
 * @method static Builder|Order whereOriginalUpfrontDiscountsCustomerAmount($value)
 * @method static Builder|Order whereOriginalUpfrontDiscountsShopAmount($value)
 * @method static Builder|Order whereOriginalUpfrontGrossSalesCustomerAmount($value)
 * @method static Builder|Order whereOriginalUpfrontGrossSalesShopAmount($value)
 * @method static Builder|Order whereOutstandingCustomerAmount($value)
 * @method static Builder|Order whereOutstandingShopAmount($value)
 * @method static Builder|Order wherePaymentTermsId($value)
 * @method static Builder|Order wherePaymentTermsName($value)
 * @method static Builder|Order wherePaymentTermsType($value)
 * @method static Builder|Order whereShopCurrency($value)
 * @method static Builder|Order whereSourceId($value)
 * @method static Builder|Order whereStatus($value)
 * @method static Builder|Order whereStoreId($value)
 * @method static Builder|Order whereTags($value)
 * @method static Builder|Order whereTaxesExempt($value)
 * @method static Builder|Order whereTaxesIncluded($value)
 * @method static Builder|Order whereTbybNetSalesCustomerAmount($value)
 * @method static Builder|Order whereTbybNetSalesShopAmount($value)
 * @method static Builder|Order whereTbybRefundDiscountsCustomerAmount($value)
 * @method static Builder|Order whereTbybRefundDiscountsShopAmount($value)
 * @method static Builder|Order whereTbybRefundGrossSalesCustomerAmount($value)
 * @method static Builder|Order whereTbybRefundGrossSalesShopAmount($value)
 * @method static Builder|Order whereTest($value)
 * @method static Builder|Order whereTotalCustomerAmount($value)
 * @method static Builder|Order whereTotalNetSalesCustomerAmount($value)
 * @method static Builder|Order whereTotalNetSalesShopAmount($value)
 * @method static Builder|Order whereTotalOrderLevelRefundsCustomerAmount($value)
 * @method static Builder|Order whereTotalOrderLevelRefundsShopAmount($value)
 * @method static Builder|Order whereTotalShopAmount($value)
 * @method static Builder|Order whereTrialExpiresAt($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUpfrontNetSalesCustomerAmount($value)
 * @method static Builder|Order whereUpfrontNetSalesShopAmount($value)
 * @method static Builder|Order whereUpfrontRefundDiscountsCustomerAmount($value)
 * @method static Builder|Order whereUpfrontRefundDiscountsShopAmount($value)
 * @method static Builder|Order whereUpfrontRefundGrossSalesCustomerAmount($value)
 * @method static Builder|Order whereUpfrontRefundGrossSalesShopAmount($value)
 * @method static Builder|Order withTrashed()
 * @method static Builder|Order withoutCurrentStore()
 * @method static Builder|Order withoutTrashed()
 * @mixin Eloquent
 */
class Order extends Model
{
    use HasModelFactory;
    use SoftDeletes;
    use HasUuids;
    use CurrentStore;

    protected $fillable = [
        'store_id',
        'source_id',
        'status',
        'order_data',
        'blackcart_metadata',
        'name',
        'taxes_included',
        'taxes_exempt',
        'tags',
        'discount_codes',
        'test',
        'payment_terms_id',
        'payment_terms_name',
        'payment_terms_type',
        'shop_currency',
        'customer_currency',
        'total_shop_amount',
        'total_customer_amount',
        'outstanding_shop_amount',
        'outstanding_customer_amount',
        'original_outstanding_shop_amount',
        'original_outstanding_customer_amount',
        'original_tbyb_gross_sales_shop_amount',
        'original_tbyb_gross_sales_customer_amount',
        'original_upfront_gross_sales_shop_amount',
        'original_upfront_gross_sales_customer_amount',
        'original_total_gross_sales_shop_amount',
        'original_total_gross_sales_customer_amount',
        'original_tbyb_discounts_shop_amount',
        'original_tbyb_discounts_customer_amount',
        'original_upfront_discounts_shop_amount',
        'original_upfront_discounts_customer_amount',
        'original_total_discounts_shop_amount',
        'original_total_discounts_customer_amount',
        'tbyb_refund_gross_sales_shop_amount',
        'tbyb_refund_gross_sales_customer_amount',
        'upfront_refund_gross_sales_shop_amount',
        'upfront_refund_gross_sales_customer_amount',
        'total_order_level_refunds_shop_amount',
        'total_order_level_refunds_customer_amount',
        'tbyb_refund_discounts_shop_amount',
        'tbyb_refund_discounts_customer_amount',
        'upfront_refund_discounts_shop_amount',
        'upfront_refund_discounts_customer_amount',
        'tbyb_net_sales_shop_amount',
        'tbyb_net_sales_customer_amount',
        'upfront_net_sales_shop_amount',
        'upfront_net_sales_customer_amount',
        'total_net_sales_shop_amount',
        'total_net_sales_customer_amount',
        'completed_at',
        'assumed_delivery_merchant_email_sent_at',
        'trial_expires_at',
    ];

    protected $with = [
        'lineItems',
        'refunds',
        'returns',
        'transactions',
    ];

    protected $table = 'orders';

    protected $dispatchesEvents = [
        'created' => OrderCreatedEvent::class,
    ];

    protected function casts(): array
    {
        return [
            'order_data' => 'array',
            'blackcart_metadata' => 'array',
            'status' => OrderStatus::class,
            'created_at' => 'datetime:Y-m-d\TH:i:sP',
            'shop_currency' => CurrencyAlpha3::class,
            'customer_currency' => CurrencyAlpha3::class,
            'total_shop_amount' => MoneyCast::class . ':shop_currency',
            'total_customer_amount' => MoneyCast::class . ':customer_currency',
            'outstanding_shop_amount' => MoneyCast::class . ':shop_currency',
            'outstanding_customer_amount' => MoneyCast::class . ':customer_currency',
            'original_outstanding_shop_amount' => MoneyCast::class . ':shop_currency',
            'original_outstanding_customer_amount' => MoneyCast::class . ':customer_currency',
            'original_tbyb_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'original_tbyb_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'original_upfront_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'original_upfront_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'original_total_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'original_total_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'original_tbyb_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'original_tbyb_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'original_upfront_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'original_upfront_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'original_total_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'original_total_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_refund_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_refund_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'upfront_refund_gross_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_refund_gross_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'total_order_level_refunds_shop_amount' => MoneyCast::class . ':shop_currency',
            'total_order_level_refunds_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_refund_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_refund_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'upfront_refund_discounts_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_refund_discounts_customer_amount' => MoneyCast::class . ':customer_currency',
            'tbyb_net_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'tbyb_net_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'upfront_net_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'upfront_net_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'total_net_sales_shop_amount' => MoneyCast::class . ':shop_currency',
            'total_net_sales_customer_amount' => MoneyCast::class . ':customer_currency',
            'completed_at' => 'datetime',
            'assumed_delivery_merchant_email_sent_at' => 'datetime',
            'trial_expires_at' => 'datetime',
        ];
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
