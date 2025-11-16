<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Enums\Exceptions\ApiExceptionTypes;
use Throwable;

/**
 * @param int<400,499> $code
 */
class ClientApiException extends ApiException
{
    public function __construct(
        string $message = 'Bad request, please fix your request before retrying.',
        int $code = 400,
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::INVALID_REQUEST,
        Throwable $previous = null,
    ) {
        parent::__construct($message, $code, ApiExceptionTypes::REQUEST_ERROR, $errorCode, $previous);
    }
}
