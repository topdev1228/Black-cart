<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions;

use Exception;
use Throwable;

class UndefinedPropertyException extends Exception
{
    /**
     * @psalm-param class-string $class
     */
    public function __construct(string $property, string $class, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Property %s does not exist on %s.', $property, $class), previous: $previous);
    }
}
