<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions\Shopify;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Exceptions\ClientApiException;
use Throwable;

class ShopifyClientException extends ClientApiException
{
    public function __construct(
        string $message = 'Bad request, please fix your request before retrying.',
        int $code = 400,
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::INVALID_REQUEST,
        Throwable $previous = null
    ) {
        parent::__construct('Shopify ' . $message, $code, $errorCode, $previous);
    }
}
