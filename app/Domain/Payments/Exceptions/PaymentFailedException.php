<?php
declare(strict_types=1);

namespace App\Domain\Payments\Exceptions;

use App\Exceptions\ServerApiException;

class PaymentFailedException extends ServerApiException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
