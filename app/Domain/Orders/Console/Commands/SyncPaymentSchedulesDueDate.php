<?php
declare(strict_types=1);

namespace App\Domain\Orders\Console\Commands;

use App;
use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Repositories\InternalOrderRepository;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Services\ShopifyOrderService;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Stores\Repositories\StoreRepository;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Output\ConsoleOutput;

class SyncPaymentSchedulesDueDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:sync-payment-schedules-due-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get in-trial orders and sync their payment schedules due date.';

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
        ShopifyOrderService $shopifyOrderService,
        StoreRepository $storeRepository,
    ): void {
        $orders = $internalOrderRepository->getInTrialOrdersWithNullTrialExpiryDate();

        foreach ($orders as $order) {
            $this->info('Sync payment schedule due date for order ' . $order->id . ' - start');

            try {
                App::context()->store = $storeRepository->getByIdUnsafe($order->storeId);
            } catch (Exception $e) {
                $this->error('Store not found');
                continue;
            }

            $orderRefreshed = $orderService->getById($order->id);

            // Assume that the latest line item's updated_at is the trial start date
            $trialStartedAt = $orderRefreshed->createdAt;
            foreach ($orderRefreshed->lineItems as $lineItem) {
                if ($lineItem->updatedAt->gt($trialStartedAt)) {
                    $trialStartedAt = $lineItem->updatedAt;
                }
            }
            $this->info('Order trialStartedAt: ' . $trialStartedAt->toDateTimeString());

            $program = $this->getProgram();
            if ($program) {
                $tryPeriodLength = $program->tryPeriodDays + $program->dropOffDays;
                $this->info('Program found, try period length: ' . $tryPeriodLength . ' days');
            } else {
                $defaultProgram = ProgramValue::from(['store_id' => App::context()->store->id]);
                $tryPeriodLength = $defaultProgram->tryPeriodDays + $defaultProgram->dropOffDays;
                $this->info('Program not found, using defaults, try period length: ' . $tryPeriodLength . ' days');
            }
            $orderRefreshed->trialExpiresAt = $trialStartedAt->addDays($tryPeriodLength);

            $orderRefreshed = $orderService->update($orderRefreshed);
            $this->info('Order trialExpiresAt updated: ' . $orderRefreshed->trialExpiresAt->toDateTimeString());

            // Sync the order's trial_expiry_date to the Shopify order's payment schedules due date
            try {
                $shopifyOrderService->updateShopifyPaymentScheduleDueDate($orderRefreshed->paymentTermsId, $orderRefreshed->trialExpiresAt);
                $this->info('Payment schedules due date synced in Shopify for order ' . $orderRefreshed->id);
            } catch (Exception $e) {
                $this->error('Failed to sync payment schedules due date for order ' . $order->id . ': ' . $e->getMessage());
            }

            if ($orderRefreshed->trialExpiresAt->isPast()) {
                PaymentRequiredEvent::dispatch(
                    $orderRefreshed->id,
                    $orderRefreshed->sourceId,
                    $orderRefreshed->id,
                    $orderRefreshed->outstandingCustomerAmount
                );
                $this->info('Payment required event dispatched for order ' . $orderRefreshed->id);
            } else {
                $this->info('Order expires in the future');
            }
            $this->info('Done');
        }
    }

    public function getProgram(): ?ProgramValue
    {
        $response = Http::get('http://localhost:8080/api/stores/programs');
        $programData = $response['programs'][0] ?? null;

        return $programData ? ProgramValue::from($programData) : null;
    }
}
