<?php
declare(strict_types=1);

namespace App\Domain\Products\Http\Controllers;

use App\Domain\Products\Services\ShopifyProductService;

class ProductsController
{
    public function __construct(protected ShopifyProductService $productService)
    {
    }

    public function get(string $id)
    {
        return $this->productService->getProduct($id);
    }
}
