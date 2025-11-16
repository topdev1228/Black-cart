<?php
declare(strict_types=1);

namespace App\Domain\Billings\Repositories;

use App\Domain\Billings\Models\TbybNetSale;
use App\Domain\Billings\Values\TbybNetSale as TbybNetSaleValue;

class TbybNetSaleRepository
{
    public function store(TbybNetSaleValue $tbybNetSaleValue): TbybNetSaleValue
    {
        return TbybNetSaleValue::from(TbybNetSale::updateOrCreate([
            'store_id' => $tbybNetSaleValue->storeId,
            'time_range_start' => $tbybNetSaleValue->timeRangeStart,
            'time_range_end' => $tbybNetSaleValue->timeRangeEnd,
        ], $tbybNetSaleValue->toArray()));
    }
}
