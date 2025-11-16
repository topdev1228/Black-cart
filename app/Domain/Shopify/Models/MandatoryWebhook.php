<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Models;

use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use App\Domain\Shopify\Enums\MandatoryWebhookStatus;
use App\Domain\Shopify\Enums\MandatoryWebhookTopic;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Domain\Shopify\Models\MandatoryWebhook
 *
 * @property string $id
 * @property string $store_id
 * @property MandatoryWebhookTopic $topic
 * @property string $shopify_shop_id
 * @property string $shopify_domain
 * @property array $data
 * @property MandatoryWebhookStatus $status
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @method static Builder|MandatoryWebhook newModelQuery()
 * @method static Builder|MandatoryWebhook newQuery()
 * @method static Builder|MandatoryWebhook onlyTrashed()
 * @method static Builder|MandatoryWebhook query()
 * @method static Builder|MandatoryWebhook whereCreatedAt($value)
 * @method static Builder|MandatoryWebhook whereData($value)
 * @method static Builder|MandatoryWebhook whereDeletedAt($value)
 * @method static Builder|MandatoryWebhook whereId($value)
 * @method static Builder|MandatoryWebhook whereShopifyDomain($value)
 * @method static Builder|MandatoryWebhook whereShopifyShopId($value)
 * @method static Builder|MandatoryWebhook whereStatus($value)
 * @method static Builder|MandatoryWebhook whereStoreId($value)
 * @method static Builder|MandatoryWebhook whereTopic($value)
 * @method static Builder|MandatoryWebhook whereUpdatedAt($value)
 * @method static Builder|MandatoryWebhook withTrashed()
 * @method static Builder|MandatoryWebhook withoutCurrentStore()
 * @method static Builder|MandatoryWebhook withoutTrashed()
 * @mixin Eloquent
 */
class MandatoryWebhook extends Model
{
    use HasUuids;
    use HasModelFactory;
    use SoftDeletes;
    use CurrentStore;
    use HasTimestamps;

    protected $table = 'shopify_mandatory_webhooks';

    protected $fillable = [
        'store_id',
        'topic',
        'shopify_shop_id',
        'shopify_domain',
        'data',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'topic' => MandatoryWebhookTopic::class,
            'data' => 'array',
            'status' => MandatoryWebhookStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
