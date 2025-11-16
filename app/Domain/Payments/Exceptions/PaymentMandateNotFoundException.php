<?php
declare(strict_types=1);

namespace App\Domain\Payments\Exceptions;

use App\Exceptions\ServerApiException;

class PaymentMandateNotFoundException extends ServerApiException
{
    public function __construct()
    {
        parent::__construct('Payment mandate not found');
    }
}
