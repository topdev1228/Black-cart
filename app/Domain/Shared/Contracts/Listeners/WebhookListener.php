<?php
declare(strict_types=1);

namespace App\Domain\Shared\Contracts\Listeners;

use App\Domain\Shared\Values\Value;
use Illuminate\Support\Collection;

interface WebhookListener
{
    public function handle(Value|Collection|array $data): void;
}
