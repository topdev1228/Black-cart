<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\StringTransform;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class WebhookRefundsCreate extends Value
{
    use HasValueFactory;

    public function __construct(
        #[MapInputName('id')]
        #[WithCast(StringTransform::class, 'shopifyGid:Refund')]
        public string $sourceId,
        public ?WebhookRefundsCreateDuties $duties = null,
        #[MapInputName('order_adjustments')]
        #[DataCollectionOf(WebhookRefundsCreateOrderAdjustment::class)]
        public ?DataCollection $orderLevelRefundAdjustments = null,
        #[DataCollectionOf(WebhookRefundsCreateRefundLineItem::class)]
        public ?DataCollection $refundLineItems = null,
        #[DataCollectionOf(WebhookRefundsCreateTransaction::class)]
        public ?DataCollection $transactions = null,
        #[MapInputName('order_id')]
        #[WithCast(StringTransform::class, 'shopifyGid:Order')]
        public string $sourceOrderId = ''
    ) {
    }
}
