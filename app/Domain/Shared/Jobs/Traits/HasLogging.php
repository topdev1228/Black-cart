<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Traits;

use App\Domain\Shared\Jobs\Middleware\Logging;

/**
 * @mixin \App\Domain\Shared\Jobs\BaseJob
 */
trait HasLogging
{
    use HasMiddleware;

    /**
     * @return array<Logging>
     */
    public function middlewareHasLogging(): array
    {
        return [
            resolve(Logging::class),
        ];
    }
}
