<?php
declare(strict_types=1);

namespace App\Domain\Billings\Models;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Billings\Models\Subscription
 *
 * @property string $id
 * @property string $store_id
 * @property string $shopify_app_subscription_id
 * @property string $shopify_confirmation_url
 * @property SubscriptionStatus $status
 * @property CarbonImmutable|null $current_period_start
 * @property CarbonImmutable|null $current_period_end
 * @property int $trial_days
 * @property CarbonImmutable|null $trial_period_end
 * @property int $is_test
 * @property CarbonImmutable|null $activated_at
 * @property CarbonImmutable|null $deactivated_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read Collection<int, \App\Domain\Billings\Models\SubscriptionLineItem> $subscriptionLineItems
 * @property-read int|null $subscription_line_items_count
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription onlyTrashed()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereActivatedAt($value)
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereCurrentPeriodEnd($value)
 * @method static Builder|Subscription whereCurrentPeriodStart($value)
 * @method static Builder|Subscription whereDeactivatedAt($value)
 * @method static Builder|Subscription whereDeletedAt($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereIsTest($value)
 * @method static Builder|Subscription whereShopifyAppSubscriptionId($value)
 * @method static Builder|Subscription whereShopifyConfirmationUrl($value)
 * @method static Builder|Subscription whereStatus($value)
 * @method static Builder|Subscription whereStoreId($value)
 * @method static Builder|Subscription whereTrialDays($value)
 * @method static Builder|Subscription whereTrialPeriodEnd($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @method static Builder|Subscription withTrashed()
 * @method static Builder|Subscription withoutCurrentStore()
 * @method static Builder|Subscription withoutTrashed()
 * @mixin Eloquent
 */
class Subscription extends Model
{
    use HasUuids;
    use HasModelFactory;
    use SoftDeletes;
    use CurrentStore;
    use HasTimestamps;

    protected $table = 'billings_subscriptions';

    protected $fillable = [
        'store_id',
        'shopify_app_subscription_id',
        'shopify_confirmation_url',
        'status',
        'current_period_start',
        'current_period_end',
        'trial_days',
        'trial_period_end',
        'is_test',
        'activated_at',
        'deactivated_at',
    ];

    protected $with = [
        'subscriptionLineItems',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'trial_period_end' => 'datetime',
            'activated_at' => 'datetime',
            'deactivated_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function subscriptionLineItems(): HasMany
    {
        return $this->hasMany(SubscriptionLineItem::class);
    }
}
