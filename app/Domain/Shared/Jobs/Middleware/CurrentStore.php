<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Middleware;

use App\Domain\Shared\Contracts\Jobs\Middleware;
use App\Domain\Shared\Jobs\BaseJob;
use App\Domain\Stores\Repositories\StoreRepository;
use Illuminate\Support\Facades\App;

class CurrentStore implements Middleware
{
    public function __construct(protected StoreRepository $storeRepository)
    {
    }

    public function handle(BaseJob $job, callable $next): ?Middleware
    {
        if (isset($job->metadata['store']->id) && !empty($job->metadata['store']->id)) {
            App::context(store: $this->storeRepository->getByIdUnsafe($job->metadata['store']->id));
        }

        return $next($job);
    }
}
