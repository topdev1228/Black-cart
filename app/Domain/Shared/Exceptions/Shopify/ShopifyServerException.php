<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions\Shopify;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Exceptions\ServerApiException;
use Throwable;

class ShopifyServerException extends ServerApiException
{
    public function __construct(
        string $message = 'Something went wrong, please try again in a few minutes.',
        int $code = 500,
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::SERVER_ERROR,
        Throwable $previous = null
    ) {
        parent::__construct('Shopify ' . $message, $code, $errorCode, $previous);
    }
}
