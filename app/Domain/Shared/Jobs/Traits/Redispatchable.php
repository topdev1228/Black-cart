<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Traits;

use Illuminate\Foundation\Bus\PendingClosureDispatch;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @mixin \App\Domain\Shared\Jobs\BaseJob
 */
trait Redispatchable
{
    use HasMetadata;

    public function redispatch(): PendingClosureDispatch|PendingDispatch
    {
        $this->attempt();

        // remove inner Stackkit job as it contains a closure and will fail during serialization
        $this->job = null;

        return dispatch($this);
    }
}
