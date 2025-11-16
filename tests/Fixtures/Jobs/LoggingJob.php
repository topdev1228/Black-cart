<?php
declare(strict_types=1);

namespace Tests\Fixtures\Jobs;

use App\Domain\Shared\Jobs\BaseJob;
use App\Domain\Shared\Jobs\Traits\HasLogging;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class LoggingJob extends BaseJob
{
    use Dispatchable;
    use HasLogging;

    public function __construct(protected bool $redispatch = false)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        Log::debug('[test] Handle method called');
        if ($this->redispatch) {
            $this->redispatch = false;
            $this->redispatch();
        }
    }
}
