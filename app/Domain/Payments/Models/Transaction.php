<?php
declare(strict_types=1);

namespace App\Domain\Payments\Models;

use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Shared\Models\Casts\Money as MoneyCast;
use App\Domain\Shared\Traits\HasModelFactory;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Payments\Models\Transaction
 *
 * @property string $id
 * @property string|null $source_id
 * @property string|null $transaction_source_name
 * @property string $order_id
 * @property string $store_id
 * @property string $source_order_id
 * @property CarbonImmutable|null $authorization_expires_at
 * @property \Brick\Money\Money $shop_amount
 * @property CurrencyAlpha3 $shop_currency
 * @property \Brick\Money\Money $customer_amount
 * @property CurrencyAlpha3 $customer_currency
 * @property TransactionStatus $status
 * @property TransactionKind $kind
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property string|null $captured_transaction_id
 * @property string|null $captured_transaction_source_id
 * @property string|null $parent_transaction_id
 * @property string|null $parent_transaction_source_id
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction onlyTrashed()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereAuthorizationExpiresAt($value)
 * @method static Builder|Transaction whereCapturedTransactionId($value)
 * @method static Builder|Transaction whereCapturedTransactionSourceId($value)
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereCustomerAmount($value)
 * @method static Builder|Transaction whereCustomerCurrency($value)
 * @method static Builder|Transaction whereDeletedAt($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereKind($value)
 * @method static Builder|Transaction whereOrderId($value)
 * @method static Builder|Transaction whereParentTransactionId($value)
 * @method static Builder|Transaction whereParentTransactionSourceId($value)
 * @method static Builder|Transaction whereShopAmount($value)
 * @method static Builder|Transaction whereShopCurrency($value)
 * @method static Builder|Transaction whereSourceId($value)
 * @method static Builder|Transaction whereSourceOrderId($value)
 * @method static Builder|Transaction whereStatus($value)
 * @method static Builder|Transaction whereStoreId($value)
 * @method static Builder|Transaction whereTransactionSourceName($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 * @method static Builder|Transaction withTrashed()
 * @method static Builder|Transaction withoutTrashed()
 * @mixin Eloquent
 */
class Transaction extends Model
{
    use HasModelFactory;
    use HasUuids;
    use SoftDeletes;

    protected $table = 'payments_transactions';

    protected $fillable = [
        'source_id',
        'order_id',
        'store_id',
        'source_order_id',
        'shop_amount',
        'shop_currency',
        'customer_amount',
        'transaction_source_name',
        'customer_currency',
        'authorization_expires_at',
        'kind',
        'status',
        'created_at',
        'captured_transaction_id',
        'captured_transaction_source_id',
        'parent_transaction_id',
        'parent_transaction_source_id',
    ];

    protected function casts(): array
    {
        return [
            'shop_amount' => MoneyCast::class . ':shop_currency',
            'shop_currency' => CurrencyAlpha3::class,
            'customer_amount' => MoneyCast::class . ':customer_currency',
            'customer_currency' => CurrencyAlpha3::class,
            'kind' => TransactionKind::class,
            'status' => TransactionStatus::class,
            'authorization_expires_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
