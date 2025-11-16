<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Enums\FulfillmentEventStatus;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class FulfillmentEvent extends Value
{
    use HasValueFactory;

    public function __construct(
        public int $id,
        public int $fulfillmentId,
        public FulfillmentEventStatus $status,
        public ?string $message = null,
        public ?string $happened_at = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $country = null,
        public ?string $zip = null,
        public ?string $address1 = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?int $shop_id = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $estimatedDeliveryAt = null,
        public ?int $orderId = null,
        public ?string $adminGraphqlApiId = null
    ) {
    }
}
