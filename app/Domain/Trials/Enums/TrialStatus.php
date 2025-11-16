<?php
declare(strict_types=1);

namespace App\Domain\Trials\Enums;

enum TrialStatus: string
{
    case INIT = 'init';
    case PRETRIAL = 'pre-trial';
    case TRIAL = 'trial';

    case POSTTRIAL = 'post-trial';
    case COMPLETE = 'complete';

    case CANCELLED = 'cancelled';
}
