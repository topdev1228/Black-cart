<?php
declare(strict_types=1);

namespace App\Domain\Billings\Http\Controllers;

use App\Domain\Billings\Http\Resources\SubscriptionResource;
use App\Domain\Billings\Services\SubscriptionService;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SubscriptionsController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService)
    {
    }

    public function store(): Response|JsonResponse
    {
        return $this->sendResponse(
            new SubscriptionResource(
                $this->subscriptionService->create(new SubscriptionValue(storeId: App()::context()->store->id), App()::context()->store->domain)
            ),
            Response::HTTP_CREATED
        );
    }

    public function getActive(): Response|JsonResponse
    {
        return $this->sendResponse(
            new SubscriptionResource(
                $this->subscriptionService->getActiveSubscription()
            )
        );
    }
}
