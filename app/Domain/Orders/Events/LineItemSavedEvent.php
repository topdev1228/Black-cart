<?php
declare(strict_types=1);

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Models\LIneItem;
use App\Domain\Orders\Values\LineItem as LineItemValue;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LineItemSavedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    public LineItemValue $lineItem;

    /**
     * Create a new event instance.
     */
    public function __construct(LineItem $lineItemModel)
    {
        $this->lineItem = LineItemValue::from($lineItemModel);
    }
}
