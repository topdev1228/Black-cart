<?php
declare(strict_types=1);

namespace App\Domain\Programs\Http\Controllers;

use App\Domain\Programs\Services\ProgramProductService;
use App\Domain\Shared\Http\Controllers\Controller;

class ProgramProductsController extends Controller
{
    public function __construct(protected ProgramProductService $programProductService)
    {
    }

    public function getProducts(string $id)
    {
        return $this->programProductService->getProducts($id);
    }
}
