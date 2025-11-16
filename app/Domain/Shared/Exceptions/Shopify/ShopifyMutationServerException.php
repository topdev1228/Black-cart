<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions\Shopify;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use Throwable;

class ShopifyMutationServerException extends ShopifyServerException
{
    public function __construct(
        string $mutation,
        string $message,
        int $code = 500,
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::SERVER_ERROR,
        Throwable $previous = null
    ) {
        parent::__construct($mutation . ': ' . $message, $code, $errorCode, $previous);
    }
}
