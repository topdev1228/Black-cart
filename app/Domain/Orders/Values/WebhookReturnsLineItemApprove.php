<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\StringTransform;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookReturnsLineItemApprove extends Value
{
    use HasValueFactory;

    public function __construct(
        public int $id,
        #[WithTransformer(StringTransform::class, 'shopifyId')]
        public string $adminGraphqlApiId,
        public WebhookReturnsLineItemFulfillmentApprove $fulfillmentLineItem,
        public int $quantity,
        public string $returnReason,
        public ?string $returnReasonNote = null,
        public ?string $customerNote = null,
    ) {
    }
}
