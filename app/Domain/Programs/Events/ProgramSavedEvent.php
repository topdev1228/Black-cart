<?php
declare(strict_types=1);

namespace App\Domain\Programs\Events;

use App\Domain\Programs\Models\Program;
use App\Domain\Programs\Values\Program as ProgramValue;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(Program $program)
 */
class ProgramSavedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;
    use Broadcastable;

    public ProgramValue $program;

    /**
     * Create a new event instance.
     */
    public function __construct(Program $programModel)
    {
        $this->program = ProgramValue::from($programModel);
    }
}
