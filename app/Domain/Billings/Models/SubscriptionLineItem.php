<?php
declare(strict_types=1);

namespace App\Domain\Billings\Models;

use App\Domain\Billings\Enums\SubscriptionLineItemType;
use App\Domain\Shared\Models\Casts\Money;
use App\Domain\Shared\Traits\HasModelFactory;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * App\Domain\Billings\Models\SubscriptionLineItem
 *
 * @property string $id
 * @property string $subscription_id
 * @property string $shopify_app_subscription_id
 * @property string $shopify_app_subscription_line_item_id
 * @property SubscriptionLineItemType $type usage|recurring
 * @property string $terms
 * @property \Brick\Money\Money|null $recurring_amount
 * @property CurrencyAlpha3 $recurring_amount_currency ISO 4217
 * @property \Brick\Money\Money|null $usage_capped_amount
 * @property CurrencyAlpha3 $usage_capped_amount_currency ISO 4217
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read \App\Domain\Billings\Models\Subscription|null $subscription
 * @method static Builder|SubscriptionLineItem newModelQuery()
 * @method static Builder|SubscriptionLineItem newQuery()
 * @method static Builder|SubscriptionLineItem onlyTrashed()
 * @method static Builder|SubscriptionLineItem query()
 * @method static Builder|SubscriptionLineItem whereCreatedAt($value)
 * @method static Builder|SubscriptionLineItem whereDeletedAt($value)
 * @method static Builder|SubscriptionLineItem whereId($value)
 * @method static Builder|SubscriptionLineItem whereRecurringAmount($value)
 * @method static Builder|SubscriptionLineItem whereRecurringAmountCurrency($value)
 * @method static Builder|SubscriptionLineItem whereShopifyAppSubscriptionId($value)
 * @method static Builder|SubscriptionLineItem whereShopifyAppSubscriptionLineItemId($value)
 * @method static Builder|SubscriptionLineItem whereSubscriptionId($value)
 * @method static Builder|SubscriptionLineItem whereTerms($value)
 * @method static Builder|SubscriptionLineItem whereType($value)
 * @method static Builder|SubscriptionLineItem whereUpdatedAt($value)
 * @method static Builder|SubscriptionLineItem whereUsageCappedAmount($value)
 * @method static Builder|SubscriptionLineItem whereUsageCappedAmountCurrency($value)
 * @method static Builder|SubscriptionLineItem withTrashed()
 * @method static Builder|SubscriptionLineItem withoutTrashed()
 * @mixin Eloquent
 */
class SubscriptionLineItem extends Model
{
    use HasUuids;
    use HasModelFactory;
    use SoftDeletes;
    use HasTimestamps;

    protected $table = 'billings_subscription_line_items';

    protected $fillable = [
        'subscription_id',
        'shopify_app_subscription_id',
        'shopify_app_subscription_line_item_id',
        'type',
        'terms',
        'recurring_amount',
        'recurring_amount_currency',
        'usage_capped_amount',
        'usage_capped_amount_currency',
    ];

    protected function casts(): array
    {
        return [
            'type' => SubscriptionLineItemType::class,
            'recurring_amount' => Money::class . ':usage_capped_amount_currency',
            'recurring_amount_currency' => CurrencyAlpha3::class,
            'usage_capped_amount' => Money::class . ':usage_capped_amount_currency',
            'usage_capped_amount_currency' => CurrencyAlpha3::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
