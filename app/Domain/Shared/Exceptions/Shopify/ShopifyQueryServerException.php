<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions\Shopify;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use Throwable;

class ShopifyQueryServerException extends ShopifyServerException
{
    public function __construct(
        string $query,
        string $message,
        int $code = 500,
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::SERVER_ERROR,
        Throwable $previous = null
    ) {
        parent::__construct($query . ': ' . $message, $code, $errorCode, $previous);
    }
}
