<?php
declare(strict_types=1);

namespace App\Domain\Stores\Listeners;

use App;
use App\Domain\Stores\Services\StoreService;

class WebhookAppUninstalledListener
{
    public function __construct(protected StoreService $storeService)
    {
    }

    public function handle(): void
    {
        $this->storeService->delete(App::context()->store);
    }
}
