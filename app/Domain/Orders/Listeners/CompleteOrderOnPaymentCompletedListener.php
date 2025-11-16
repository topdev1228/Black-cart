<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Mail\TrialComplete;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\PaymentCompleteEvent;
use Feature;
use Mail;

class CompleteOrderOnPaymentCompletedListener
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
    public function handle(PaymentCompleteEvent $eventData): void
    {
        $order = $this->orderService->getBySourceId($eventData->sourceOrderId);
        if ($eventData->outstandingAmountZeroAlready) {
            $this->orderService->completeOrder($order->id);
        }
        $this->orderService->addCompleteTagsToOrder($order);

        // TODO: make this a store setting to be exposed on Shoipfy Blackcart Admin
        if (Feature::enabled('shopify-perm-b-merchant-trial-ended-email')) {
            // Merchant is sending this email
            return;
        }

        Mail::to($order->customerEmail())->send(new TrialComplete($order));
    }
}
