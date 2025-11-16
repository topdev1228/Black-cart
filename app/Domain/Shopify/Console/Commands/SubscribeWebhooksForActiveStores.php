<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Console\Commands;

use App;
use App\Domain\Shopify\Services\WebhooksService;
use App\Domain\Stores\Repositories\InternalStoreRepository;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class SubscribeWebhooksForActiveStores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:subscribe-webhooks-for-active-stores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to all Shopify webhooks for active stores';

    public function __construct()
    {
        parent::__construct();

        $this->output = new ConsoleOutput();
    }

    /**
     * Execute the console command.
     */
    public function handle(WebhooksService $webhooksService, InternalStoreRepository $internalStoreRepository): void
    {
        $stores = $internalStoreRepository->getAllUndeleted();

        foreach ($stores as $store) {
            if ($store->accessToken === null) {
                $this->info('Skip store because it has no access token, store ID = ' . $store->id . ' , domain = ' . $store->domain);
                continue;
            }
            App::context()->store = $store;
            $this->info('Subscribe to Shopify webhooks start, store ID = ' . $store->id . ' , domain = ' . $store->domain);
            try {
                $webhooksService->subscribe();
            } catch (\Exception $e) {
                $this->info('Failed, error = ' . $e->getMessage());
            }
            $this->info('Completed');
        }
    }
}
