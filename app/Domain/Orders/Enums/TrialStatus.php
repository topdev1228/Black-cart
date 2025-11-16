<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum TrialStatus: string
{
    case INIT = 'init';
    case PRE_TRIAL = 'pre-trial';
    case TRIAL = 'trial';

    case POST_TRIAL = 'post-trial';
    case COMPLETE = 'complete';
}
