<?php
declare(strict_types=1);

namespace App\Domain\Payments\Exceptions;

use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Enums\Exceptions\ApiExceptionErrorCodes;

class ShopifyMandatePaymentOutstandingAmountZeroException extends ShopifyClientException
{
    const MESSAGE = 'Shopify orderCreateMandatePayment: total_outstanding amount must be greater than zero.';

    public function __construct()
    {
        parent::__construct(static::MESSAGE, 422, ApiExceptionErrorCodes::INVALID_PARAMETERS);
    }
}
