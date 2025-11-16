<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Listeners;

use App\Domain\Shopify\Services\MetafieldsService;
use App\Domain\Shopify\Values\ProgramSavedEvent;

class ProgramSavedUpdateMetafieldsListener
{
    public function __construct(protected MetafieldsService $metafieldsService)
    {
    }

    public function handle(ProgramSavedEvent $event): void
    {
        $this->metafieldsService->upsertProgramMetafields($event->program);
    }
}
