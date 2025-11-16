<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Casts\SafeEnum;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookShopifyAppSubscription extends Value
{
    use HasValueFactory;

    // 'admin_graphql_api_id': 'gid://shopify/AppSubscription/1029266996',
    // "name": "Webhook Test",
    // "status": "PENDING",
    // "admin_graphql_api_shop_id": "gid://shopify/Shop/548380009",
    // "created_at": "2021-12-31T19:00:00-05:00",
    // "updated_at": "2021-12-31T19:00:00-05:00",
    // "currency": "USD",
    // "capped_amount": "20.0"

    public function __construct(
        #[MapInputName('admin_graphql_api_id')]
        public string $id,
        public string $name,
        #[WithCast(SafeEnum::class, 'lower')]
        public SubscriptionStatus $status,
        #[MapName('admin_graphql_api_shop_id')]
        public string $shopId,
        #[WithCast(MoneyValue::class)]
        public Money $cappedAmount,
        public CurrencyAlpha3 $currency,
        public CarbonImmutable $createdAt,
        public CarbonImmutable $updatedAt,
    ) {
    }
}
