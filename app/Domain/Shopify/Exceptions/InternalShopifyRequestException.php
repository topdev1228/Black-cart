<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Exceptions;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Exceptions\ServerApiException;
use Throwable;

class InternalShopifyRequestException extends ServerApiException
{
    public function __construct(
        string $message = 'Internal call to Shopify failed, please try again in a few minutes.',
        Throwable $previous = null
    ) {
        parent::__construct($message, 500, ApiExceptionErrorCodes::SERVER_ERROR, $previous);
    }
}
