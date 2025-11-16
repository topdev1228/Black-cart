<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Repositories\TbybNetSaleRepository;
use App\Domain\Orders\Values\TbybNetSale as TbybNetSaleValue;

class TbybNetSaleService
{
    public function __construct(
        protected TbybNetSaleRepository $tbybNetSaleRepository,
    ) {
    }

    public function create(TbybNetSaleValue $tbybNetSale): TbybNetSaleValue
    {
        return $this->tbybNetSaleRepository->create($tbybNetSale);
    }

    public function getLatest(): TbybNetSaleValue
    {
        return $this->tbybNetSaleRepository->getLatest();
    }
}
