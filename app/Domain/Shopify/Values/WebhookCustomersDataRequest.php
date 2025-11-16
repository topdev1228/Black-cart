<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class WebhookCustomersDataRequest extends Value
{
    use HasValueFactory;

    /*
        {
          "shop_id": 954889,
          "shop_domain": "{shop}.myshopify.com",
          "orders_requested": [
            299938,
            280263,
            220458
          ],
          "customer": {
            "id": 191167,
            "email": "john@example.com",
            "phone": "555-625-1199"
          },
          "data_request": {
            "id": 9999
          }
        }
    */

    public function __construct(
        public string $shopId,
        public string $shopDomain,
        public array $ordersRequested,
        public WebhookCustomer $customer,
        public WebhookDataRequest $dataRequest,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'shop_id' => ['required', 'string'],
            'shop_domain' => ['required', 'string'],
            'orders_requested' => ['required', 'array'],
            'orders_requested.*' => ['int'],
            'customer' => ['required'],
            'data_request' => ['required'],
        ];
    }
}
