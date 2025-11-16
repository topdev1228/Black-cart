<?php
declare(strict_types=1);

namespace App\Domain\Orders\Models;

use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Events\TransactionCreatedEvent;
use App\Domain\Shared\Models\Casts\Money as MoneyCast;
use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Orders\Models\Transaction
 *
 * @property string $id
 * @property string $source_id
 * @property string $order_id
 * @property string $source_order_id
 * @property string $store_id
 * @property TransactionKind $kind
 * @property string $gateway
 * @property string $payment_id
 * @property string|null $parent_transaction_id
 * @property string|null $parent_transaction_source_id
 * @property CurrencyAlpha3 $customer_currency
 * @property CurrencyAlpha3 $shop_currency
 * @property \Brick\Money\Money $customer_amount
 * @property \Brick\Money\Money $shop_amount
 * @property \Brick\Money\Money $unsettled_customer_amount
 * @property \Brick\Money\Money $unsettled_shop_amount
 * @property TransactionStatus $status
 * @property bool $test
 * @property string|null $error_code
 * @property string|null $message
 * @property string|null $transaction_source_name
 * @property string|null $user_id
 * @property CarbonImmutable|null $processed_at
 * @property CarbonImmutable|null $authorization_expires_at
 * @property array $transaction_data
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Domain\Orders\Models\Order|null $order
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereAuthorizationExpiresAt($value)
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereCustomerAmount($value)
 * @method static Builder|Transaction whereCustomerCurrency($value)
 * @method static Builder|Transaction whereDeletedAt($value)
 * @method static Builder|Transaction whereErrorCode($value)
 * @method static Builder|Transaction whereGateway($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereKind($value)
 * @method static Builder|Transaction whereMessage($value)
 * @method static Builder|Transaction whereOrderId($value)
 * @method static Builder|Transaction whereParentTransactionId($value)
 * @method static Builder|Transaction whereParentTransactionSourceId($value)
 * @method static Builder|Transaction wherePaymentId($value)
 * @method static Builder|Transaction whereProcessedAt($value)
 * @method static Builder|Transaction whereShopAmount($value)
 * @method static Builder|Transaction whereShopCurrency($value)
 * @method static Builder|Transaction whereSourceId($value)
 * @method static Builder|Transaction whereSourceOrderId($value)
 * @method static Builder|Transaction whereStatus($value)
 * @method static Builder|Transaction whereStoreId($value)
 * @method static Builder|Transaction whereTest($value)
 * @method static Builder|Transaction whereTransactionData($value)
 * @method static Builder|Transaction whereTransactionSourceName($value)
 * @method static Builder|Transaction whereUnsettledCustomerAmount($value)
 * @method static Builder|Transaction whereUnsettledShopAmount($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 * @method static Builder|Transaction whereUserId($value)
 * @method static Builder|Transaction withoutCurrentStore()
 * @mixin Eloquent
 */
class Transaction extends Model
{
    use HasModelFactory;
    use HasUuids;
    use CurrentStore;

    protected $table = 'orders_transactions';

    protected $fillable = [
        'source_id',
        'store_id',
        'source_order_id',
        'order_id',
        'order_name',
        'shop_currency',
        'customer_currency',
        'shop_amount',
        'customer_amount',
        'unsettled_customer_amount',
        'unsettled_shop_amount',
        'kind',
        'gateway',
        'payment_id',
        'parent_transaction_id',
        'parent_transaction_source_id',
        'status',
        'test',
        'error_code',
        'message',
        'transaction_source_name',
        'user_id',
        'processed_at',
        'authorization_expires_at',
        'transaction_data',
    ];

    protected $dispatchesEvents = [
        'created' => TransactionCreatedEvent::class,
    ];

    protected function casts(): array
    {
        return [
            'shop_currency' => CurrencyAlpha3::class,
            'customer_currency' => CurrencyAlpha3::class,
            'shop_amount' => MoneyCast::class . ':shop_currency',
            'customer_amount' => MoneyCast::class . ':customer_currency',
            'unsettled_shop_amount' => MoneyCast::class . ':shop_currency',
            'unsettled_customer_amount' => MoneyCast::class . ':customer_currency',
            'kind' => TransactionKind::class,
            'status' => TransactionStatus::class,
            'test' => 'boolean',
            'transaction_data' => 'array',
            'authorization_expires_at' => 'datetime',
            'processed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
