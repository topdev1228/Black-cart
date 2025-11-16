<?php
declare(strict_types=1);

namespace App\Domain\Shared\Models;

use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use App\Domain\Shopify\Enums\WebhookTopic;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Shared\Models\ShopifyWebhookData
 *
 * @property string $id
 * @property string $store_id
 * @property WebhookTopic $topic
 * @property array $data
 * @property array $attributes
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @method static Builder|ShopifyWebhookData newModelQuery()
 * @method static Builder|ShopifyWebhookData newQuery()
 * @method static Builder|ShopifyWebhookData onlyTrashed()
 * @method static Builder|ShopifyWebhookData query()
 * @method static Builder|ShopifyWebhookData whereAttributes($value)
 * @method static Builder|ShopifyWebhookData whereCreatedAt($value)
 * @method static Builder|ShopifyWebhookData whereData($value)
 * @method static Builder|ShopifyWebhookData whereDeletedAt($value)
 * @method static Builder|ShopifyWebhookData whereId($value)
 * @method static Builder|ShopifyWebhookData whereStoreId($value)
 * @method static Builder|ShopifyWebhookData whereTopic($value)
 * @method static Builder|ShopifyWebhookData whereUpdatedAt($value)
 * @method static Builder|ShopifyWebhookData withTrashed()
 * @method static Builder|ShopifyWebhookData withoutCurrentStore()
 * @method static Builder|ShopifyWebhookData withoutTrashed()
 * @mixin Eloquent
 */
class ShopifyWebhookData extends Model
{
    use HasUuids;
    use HasModelFactory;
    use HasTimestamps;
    use SoftDeletes;
    use CurrentStore;

    protected $fillable = [
        'store_id',
        'topic',
        'data',
        'attributes',
    ];

    protected function casts(): array
    {
        return [
            'topic' => WebhookTopic::class,
            'data' => 'array',
            'attributes' => 'array',
        ];
    }
}
