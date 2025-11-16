<?php
declare(strict_types=1);

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Models\TbybNetSale;
use App\Domain\Orders\Values\TbybNetSale as TbybNetSaleValue;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(TbybNetSale $tbybNetSale)
 */
class TbybNetSaleCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;
    use Broadcastable;

    public TbybNetSaleValue $tbybNetSale;

    /**
     * Create a new event instance.
     */
    public function __construct(TbybNetSale $tbybNetSale)
    {
        $this->tbybNetSale = TbybNetSaleValue::from($tbybNetSale);
    }
}
