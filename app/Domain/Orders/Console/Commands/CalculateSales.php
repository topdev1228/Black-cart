<?php
declare(strict_types=1);

namespace App\Domain\Orders\Console\Commands;

use App;
use App\Domain\Orders\Jobs\CalculateSalesJob;
use App\Domain\Orders\Services\OrderService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Log;

class CalculateSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:calculate-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate sales between the given date range from transactions';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService): void
    {
        $storeIds = $orderService->getStoreIdsByDate(CarbonImmutable::now()->subDays(60));
        foreach ($storeIds as $storeId) {
            App::context()->store->id = $storeId;
            Log::info('Dispatching calculate sales job', ['store_id' => $storeId]);
            CalculateSalesJob::dispatch($storeId);
        }
    }
}
