<?php
declare(strict_types=1);

namespace App\Domain\Shared\Contracts\Jobs;

use App\Domain\Shared\Jobs\BaseJob;

interface Middleware
{
    public function handle(BaseJob $job, callable $next): ?self;
}
