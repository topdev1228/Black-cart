<?php
declare(strict_types=1);

namespace App\Domain\Orders\Console\Commands;

use App;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Repositories\InternalOrderRepository;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Payments\Events\ReAuthSuccessEvent;
use App\Domain\Payments\Jobs\ReAuthJob;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\Order as PaymentOrderValue;
use App\Domain\Stores\Repositories\StoreRepository;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class ReAuthActiveOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:re-auth-active-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-auth active orders that do not have an active authorization.';

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
        PaymentService $paymentService,
        StoreRepository $storeRepository,
    ): void {
        $orders = $internalOrderRepository->getAllActiveOrders();

        foreach ($orders as $order) {
            if (in_array($order->storeId, ['9b548a76-7570-4bc3-81c9-11add1aa50e2', '9b4c50aa-e14b-41d1-84f1-20e6745b93c7', '9b2fae42-da18-4f6b-9153-96b03c1473f0', '9b53b1d7-cfa2-441f-8ea6-42cc84a56fd9'])) {
                // Skip daveys-test-store.myshopify.com, blackcart-matthew-teststore2.myshopify.com, bestwatchclub.myshopify.com, quickstart-66ac8ce0.myshopify.com
                continue;
            }

            $this->info('Re-auth for order ' . $order->id . ' - start');

            try {
                App::context()->store = $storeRepository->getByIdUnsafe($order->storeId);
            } catch (Exception $e) {
                $this->error('Store not found');
                continue;
            }
            $this->info('Store ID ' . App::context()->store->id . ', domain = ' . App::context()->store->domain);

            $orderRefreshed = $orderService->getById($order->id);

            $authExpiresInTheFuture = false;
            foreach ($orderRefreshed->transactions as $transaction) {
                if ($transaction->kind !== TransactionKind::AUTHORIZATION) {
                    continue;
                }

                if ($transaction->status !== TransactionStatus::SUCCESS) {
                    continue;
                }

                if ($transaction->authorizationExpiresAt->isFuture()) {
                    $authExpiresInTheFuture = true;
                    break;
                }
            }

            if ($authExpiresInTheFuture) {
                $this->info('Order has an active authorization, skipping.');
                continue;
            }

            $paymentOrderValue = PaymentOrderValue::from($orderRefreshed->toArray());

            try {
                $transaction = $paymentService->createAuthHold($paymentOrderValue);
            } catch (Exception $e) {
                $this->error('Failed to re-auth order ' . $order->id . ': ' . $e->getMessage());
                continue;
            }

            if ($transaction === null) {
                $this->error('Transaction is null, no need to re-auth?');
                continue;
            }

            ReAuthSuccessEvent::dispatch($transaction->customerAmount, $paymentOrderValue->sourceId);
            ReAuthJob::dispatch($paymentOrderValue, $transaction)->delay($transaction->authorizationExpiresAt->subDay());

            $this->info('Re-authorized order ' . $order->id . ' with transaction ' . $transaction->id);
        }
    }
}
