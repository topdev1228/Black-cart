<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions\Shopify;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use Throwable;

class ShopifyMutationClientException extends ShopifyClientException
{
    public function __construct(
        string $mutation,
        string $message,
        int $code = 422,
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::INVALID_PARAMETERS,
        Throwable $previous = null
    ) {
        parent::__construct($mutation . ': ' . lcfirst($message), $code, $errorCode, $previous);
    }
}
