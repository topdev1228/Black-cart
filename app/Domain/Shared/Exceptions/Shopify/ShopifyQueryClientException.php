<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions\Shopify;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use Throwable;

class ShopifyQueryClientException extends ShopifyClientException
{
    public function __construct(
        string $query,
        string $message,
        int $code = 422,
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::INVALID_PARAMETERS,
        Throwable $previous = null
    ) {
        parent::__construct($query . ': ' . lcfirst($message), $code, $errorCode, $previous);
    }
}
