<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Mail\ReAuthSuccess;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\ReAuthSuccessEvent;
use Feature;
use Mail;

class SendReAuthSuccessEmailOnReAuthSuccessListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected OrderService $orderService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ReAuthSuccessEvent $eventData): void
    {
        if (Feature::enabled('shopify-perm-b-kill-reauth')) {
            return;
        }

        // TODO: make this a store setting to be exposed on Shoipfy Blackcart Admin
        if (Feature::enabled('shopify-perm-b-merchant-reauth-success-email')) {
            // Merchant is sending this email
            return;
        }

        $order = $this->orderService->getBySourceId($eventData->sourceOrderId);
        Mail::to($order->customerEmail())->send(new ReAuthSuccess($order, $eventData->authAmount));
    }
}
