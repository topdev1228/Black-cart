<?php
declare(strict_types=1);

namespace Tests\Fixtures\Jobs;

use App\Domain\Shared\Jobs\BaseJob;
use App\Domain\Shared\Jobs\Traits\HasDeadline;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class DeadlineJob extends BaseJob
{
    use Dispatchable;
    use HasDeadline;

    public function __construct(CarbonImmutable $deadline)
    {
        parent::__construct();
        $this->deadline($deadline);
    }

    public function handle(): void
    {
        Log::debug('[test] Handle method called');
    }
}
