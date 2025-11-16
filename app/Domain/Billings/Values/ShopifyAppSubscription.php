<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\SafeEnum;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ShopifyAppSubscription extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $id,
        #[WithCast(SafeEnum::class, 'lower')]
        public SubscriptionStatus $status,
    ) {
    }
}
