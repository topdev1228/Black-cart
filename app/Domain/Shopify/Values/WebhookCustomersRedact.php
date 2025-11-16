<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class WebhookCustomersRedact extends Value
{
    use HasValueFactory;

    /*
        {
          "shop_id": 954889,
          "shop_domain": "{shop}.myshopify.com",
          "customer": {
            "id": 191167,
            "email": "john@example.com",
            "phone": "555-625-1199"
          },
          "orders_to_redact": [
            299938,
            280263,
            220458
          ]
        }
    */

    public function __construct(
        public string $shopId,
        public string $shopDomain,
        public WebhookCustomer $customer,
        public array $ordersToRedact,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'shop_id' => ['required', 'string'],
            'shop_domain' => ['required', 'string'],
            'customer' => ['required'],
            'orders_to_redact' => ['required', 'array'],
            'orders_to_redact.*' => ['int'],
        ];
    }
}
