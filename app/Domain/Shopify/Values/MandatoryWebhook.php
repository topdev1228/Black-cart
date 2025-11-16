<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use App\Domain\Shopify\Enums\MandatoryWebhookStatus;
use App\Domain\Shopify\Enums\MandatoryWebhookTopic;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class MandatoryWebhook extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $storeId,
        public MandatoryWebhookTopic $topic,
        public string $shopifyShopId,
        public string $shopifyDomain,
        public array $data,
        public ?string $id = null,
        public MandatoryWebhookStatus $status = MandatoryWebhookStatus::PENDING,
    ) {
    }
}
