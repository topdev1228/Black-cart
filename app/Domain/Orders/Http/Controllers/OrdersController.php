<?php
declare(strict_types=1);

namespace App\Domain\Orders\Http\Controllers;

use App\Domain\Orders\Http\Resources\OrderResource;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Shared\Http\Controllers\Controller;
use App\Domain\Stores\Services\StoreService;
use App\Domain\Stores\Values\Store;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Log;

class OrdersController extends Controller
{
    public function __construct(protected OrderService $orderService, protected StoreService $storeService)
    {
    }

    // @deprecated - this is only meant for quick and dirty feature request from Shopify.  Don't use this API.
    public function endTrialForm(string $id): View
    {
        $order = $this->orderService->getUnsafeById($id);
        $this->loadStore($order->storeId);

        return view('Orders::settle', [
            'order' => $order,
            'formUrl' => route('orders.api.endTrialBeforeExpiry', [
                'id' => $order->id,
            ]),
            'jwt' => App::context()->jwtToken->token,
        ]);
    }

    // @deprecated - this is only meant for quick and dirty feature request from Shopify.  Don't use this API.
    public function endTrialBeforeExpiry(string $id): Response|JsonResponse
    {
        try {
            $this->orderService->endTrialBeforeExpiry($id);
        } catch (ModelNotFoundException $e) {
            Log::error('[Trial Cancelled] ' . $e->getMessage());

            return $this->sendResponse(
                [
                    'status' => 'error',
                ],
                404
            );
        } catch (Exception $e) {
            Log::error('[Trial Cancelled] ' . $e->getMessage());

            return $this->sendResponse(
                [
                    'status' => 'error',
                ],
                $e->getCode()
            );
        }

        return $this->sendResponse(
            [
                'status' => 'success',
            ],
            Response::HTTP_ACCEPTED,
        );
    }

    public function get(string $id): Response|JsonResponse
    {
        $order = $this->orderService->getById($id);

        return $this->sendResponse(new OrderResource($order));
    }

    protected function loadStore(string $storeId): Store
    {
        $store = $this->storeService->getStoreUnsafe($storeId);
        App::context(store: $store);

        return $store;
    }
}
