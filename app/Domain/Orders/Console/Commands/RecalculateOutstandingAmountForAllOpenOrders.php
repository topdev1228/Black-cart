<?php
declare(strict_types=1);

namespace App\Domain\Orders\Console\Commands;

use App;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\InternalOrderRepository;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Stores\Repositories\StoreRepository;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class RecalculateOutstandingAmountForAllOpenOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:recalculate-outstanding-amount-for-all-open-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all open orders and recalculate their outstanding amounts.';

    public function __construct()
    {
        parent::__construct();
        $this->output = new ConsoleOutput();
    }

    /**
     * Execute the console command.
     */
    public function handle(
        OrderService $orderService,
        InternalOrderRepository $internalOrderRepository,
        StoreRepository $storeRepository,
    ): void {
        $orders = $internalOrderRepository->getAllOrders();

        $countSuccess = 0;
        $countFailed = 0;

        foreach ($orders as $order) {
            $this->info('Recalculate order ' . $order->id . ' - start');

            try {
                App::context()->store = $storeRepository->getByIdUnsafe($order->storeId);
            } catch (Exception $e) {
                $this->error('Store not found');
                $countFailed++;
                continue;
            }

            try {
                $updatedOrder = $orderService->recalculateOrderTotals($order->id);
            } catch (Exception $e) {
                $this->error($e->getMessage());
                $countFailed++;
                continue;
            }

            if ($order->status === OrderStatus::COMPLETED && $updatedOrder->outstandingCustomerAmount->isGreaterThan(0)) {
                $this->info('Order status is completed but outstanding customer amount is > 0');
                if (!empty($updatedOrder->trialExpiresAt) && CarbonImmutable::now()->isBefore($updatedOrder->trialExpiresAt)) {
                    $this->info('Order expiry is set and is in the future');
                    $updatedOrder->status = OrderStatus::IN_TRIAL;
                } else {
                    $orderWasInTrial = false;
                    foreach ($updatedOrder->lineItems as $lineItem) {
                        if ($lineItem->status === LineItemStatus::IN_TRIAL) {
                            $orderWasInTrial = true;
                            break;
                        }
                    }
                    if ($orderWasInTrial) {
                        $this->info('Order was in trial');
                        $updatedOrder->status = OrderStatus::IN_TRIAL;
                    } else {
                        $this->info('Order was not in trial');
                        $updatedOrder->status = OrderStatus::OPEN;
                    }
                }
                $orderService->update($updatedOrder);
                $this->info('Order status updated to ' . $updatedOrder->status->value);
            }

            $this->info('done');
            $countSuccess++;
        }

        $this->info('Number of orders recalculated = ' . $countSuccess);
        $this->info('Number of orders failed = ' . $countFailed);
    }
}
