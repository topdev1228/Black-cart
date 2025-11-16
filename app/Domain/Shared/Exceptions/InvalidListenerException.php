<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions;

use Exception;

class InvalidListenerException extends Exception
{
    public function __construct(string $listener)
    {
        parent::__construct("Invalid listener: {$listener}");
    }
}
