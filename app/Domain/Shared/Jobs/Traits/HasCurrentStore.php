<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Traits;

use App\Domain\Shared\Jobs\BaseJob;
use App\Domain\Shared\Jobs\Middleware\CurrentStore;

/**
 * @mixin BaseJob
 */
trait HasCurrentStore
{
    use HasMiddleware;

    /**
     * @return array<CurrentStore>
     */
    public function middlewareHasCurrentStore(): array
    {
        return [
            resolve(CurrentStore::class),
        ];
    }
}
