<?php
declare(strict_types=1);

namespace Tests\Fixtures\Jobs;

use App\Domain\Shared\Jobs\BaseJob;
use App\Domain\Shared\Jobs\Traits\HasDeadline;
use App\Domain\Stores\Values\Store;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Assert;

/**
 * @method static PendingDispatch dispatch()
 * @method static PendingDispatch dispatchSync()
 */
class CurrentStoreJob extends BaseJob
{
    use Dispatchable;
    use HasDeadline;

    public function __construct()
    {
        parent::__construct();

        App::context(store: Store::builder()->create(['id' => null])); // unset the store so we can clear the global scope for the middleware to set
    }

    public function handle(): void
    {
        Assert::assertNotEmpty(App::context()->store->id);
        Assert::assertEquals('test-store-id', App::context()->store->id);
    }
}
