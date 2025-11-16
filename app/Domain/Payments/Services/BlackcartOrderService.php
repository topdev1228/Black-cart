<?php
declare(strict_types=1);

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Values\Order;
use Illuminate\Support\Facades\Http;

class BlackcartOrderService
{
    public function getOrderById(string $id): Order
    {
        $response = Http::get('http://localhost:8080/api/stores/orders/' . $id)->throwUnlessStatus(200)->json();

        return Order::from($response['order']);
    }
}
