<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class WebhookShopRedact extends Value
{
    use HasValueFactory;

    /*
        {
          "shop_id": 954889,
          "shop_domain": "{shop}.myshopify.com"
        }
     */

    public function __construct(
        public string $shopId,
        public string $shopDomain,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'shop_id' => ['required', 'string'],
            'shop_domain' => ['required', 'string'],
        ];
    }
}
