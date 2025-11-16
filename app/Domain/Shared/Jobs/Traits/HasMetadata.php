<?php
declare(strict_types=1);

namespace App\Domain\Shared\Jobs\Traits;

use App\Domain\Shared\Jobs\BaseJob;
use App\Domain\Stores\Values\Store;
use Illuminate\Support\Facades\App;
use Ramsey\Uuid\Uuid;

/**
 * @mixin BaseJob
 */
trait HasMetadata
{
    /**
     * @var array{uuid: string, guid: string, store: Store, attempts: int}
     */
    public array $metadata = ['attempts' => 0];

    public function setMetadata(): void
    {
        $this->metadata['store'] = App::context()->store;

        if (isset($this->metadata['uuid'])) {
            $this->metadata['guid'] = Uuid::uuid4()->toString();

            return;
        }

        $this->metadata['uuid'] = $this->metadata['guid'] = Uuid::uuid4()->toString();
    }

    public function attempt(): void
    {
        $this->metadata['attempts'] += 1;
    }

    public function attempts(): int
    {
        return $this->metadata['attempts'];
    }
}
