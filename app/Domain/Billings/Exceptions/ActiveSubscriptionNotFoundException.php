<?php
declare(strict_types=1);

namespace App\Domain\Billings\Exceptions;

use App\Exceptions\NotFoundException;

class ActiveSubscriptionNotFoundException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('No active subscription found');
    }
}
