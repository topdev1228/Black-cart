<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookShopifyAppSubscriptionsUpdate extends Value
{
    use HasValueFactory;

    public function __construct(
        public WebhookShopifyAppSubscription $appSubscription,
    ) {
    }
}
