<?php
declare(strict_types=1);

namespace App\Domain\Payments\Exceptions;

use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Enums\Exceptions\ApiExceptionErrorCodes;

class ShopifyMandatePaymentRetryFailureLimitReachedException extends ShopifyClientException
{
    const MESSAGE = "Shopify orderCreateMandatePayment: you're unable to retry payment collection using this mandate because you have reached the threshold limit for failed payments.";

    public function __construct()
    {
        parent::__construct(static::MESSAGE, 422, ApiExceptionErrorCodes::INVALID_PARAMETERS);
    }
}
