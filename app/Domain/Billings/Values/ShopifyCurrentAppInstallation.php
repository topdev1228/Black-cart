<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Billings\Values\Collections\ShopifyAppSubscriptionCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ShopifyCurrentAppInstallation extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $id,
        #[DataCollectionOf(ShopifyAppSubscription::class)]
        public ShopifyAppSubscriptionCollection $activeSubscriptions,
    ) {
    }
}
