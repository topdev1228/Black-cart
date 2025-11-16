<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Models\TbybNetSale;
use App\Domain\Orders\Values\TbybNetSale as TbybNetSaleValue;

class TbybNetSaleRepository
{
    public function __construct()
    {
    }

    public function create(TbybNetSaleValue $tbybNetSale): TbybNetSaleValue
    {
        return TbybNetSaleValue::from(TbybNetSale::create($tbybNetSale->toArray()));
    }

    public function getLatest(): TbybNetSaleValue
    {
        return TbybNetSaleValue::from(TbybNetSale::latest()->firstOrFail());
    }
}
