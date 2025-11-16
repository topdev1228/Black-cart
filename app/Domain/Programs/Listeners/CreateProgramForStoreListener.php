<?php
declare(strict_types=1);

namespace App\Domain\Programs\Listeners;

use App;
use App\Domain\Programs\Services\ProgramService;
use App\Domain\Programs\Values\Program as ProgramValue;

class CreateProgramForStoreListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected ProgramService $programService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(): void
    {
        $programValue = ProgramValue::from(
            ProgramValue::empty(),
            [
                'store_id' => App::context()->store->id,
                'currency' => App::context()->store->currency,
            ],
        );

        $this->programService->create($programValue);
    }
}
