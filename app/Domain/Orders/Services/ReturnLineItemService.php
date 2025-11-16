<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Repositories\ReturnLineItemRepository;
use App\Domain\Orders\Values\ReturnLineItem as ReturnLineItemValue;

class ReturnLineItemService
{
    public function __construct(
        protected ReturnLineItemRepository $returnLineItemRepository,
    ) {
    }

    public function save(ReturnLineItemValue $returnLineItemValue): ReturnLineItemValue
    {
        return $this->returnLineItemRepository->save($returnLineItemValue);
    }
}
