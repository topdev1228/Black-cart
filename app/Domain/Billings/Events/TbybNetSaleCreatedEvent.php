<?php
declare(strict_types=1);

namespace App\Domain\Billings\Events;

use App\Domain\Billings\Models\TbybNetSale;
use App\Domain\Billings\Values\TbybNetSale as TbybNetSaleValue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(TbybNetSale $tbybNetSale)
 */
class TbybNetSaleCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public TbybNetSaleValue $tbybNetSale;

    /**
     * Create a new event instance.
     */
    public function __construct(TbybNetSale $tbybNetSale)
    {
        $this->tbybNetSale = TbybNetSaleValue::from($tbybNetSale);
    }
}
