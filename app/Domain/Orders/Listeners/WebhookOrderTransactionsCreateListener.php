<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Services\TransactionService;
use App\Domain\Orders\Values\Transaction as TransactionValue;
use App\Domain\Orders\Values\WebhookOrderTransactionsCreate;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WebhookOrderTransactionsCreateListener
{
    public function __construct(
        protected OrderService $orderService,
        protected TransactionService $transactionService,
    ) {
    }

    public function handle(WebhookOrderTransactionsCreate $shopifyWebhookTransaction): ?TransactionValue
    {
        try {
            $orderValue = $this->orderService->getBySourceId(
                sprintf('gid://shopify/Order/%d', $shopifyWebhookTransaction->orderId),
            );
        } catch (ModelNotFoundException $e) {
            // This is a transaction that does not belong to a Blackcart order.  We can safely ignore the webhook.
            return null;
        }

        return $this->transactionService->createFromWebhook($shopifyWebhookTransaction, $orderValue);
    }
}
