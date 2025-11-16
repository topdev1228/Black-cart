<?php
declare(strict_types=1);

namespace App\Domain\Orders\Exceptions;

use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Enums\Exceptions\ApiExceptionErrorCodes;

class ShopifyUpdatePaymentScheduleDueDateOnPaidOrderException extends ShopifyClientException
{
    const MESSAGE = 'Shopify paymentTermsUpdate: cannot create payment terms on an Order that has already been paid in full.';

    public function __construct()
    {
        parent::__construct(static::MESSAGE, 422, ApiExceptionErrorCodes::INVALID_PARAMETERS);
    }
}
