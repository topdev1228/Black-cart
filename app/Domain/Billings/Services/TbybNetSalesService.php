<?php
declare(strict_types=1);

namespace App\Domain\Billings\Services;

use App\Domain\Billings\Repositories\TbybNetSaleRepository;
use App\Domain\Billings\Values\TbybNetSale as TbybNetSaleValue;

class TbybNetSalesService
{
    public function __construct(protected TbybNetSaleRepository $tbybNetSaleRepository)
    {
    }

    public function create(TbybNetSaleValue $tbybNetSaleValue): TbybNetSaleValue
    {
        return $this->tbybNetSaleRepository->store($tbybNetSaleValue);
    }
}
