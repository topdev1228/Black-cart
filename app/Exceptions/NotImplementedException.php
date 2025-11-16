<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use Throwable;

/**
 * @param int<100,599> $code
 */
class NotImplementedException extends ServerApiException
{
    public function __construct(
        string $message = 'Not implemented.',
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::NOT_IMPLEMENTED,
        Throwable $previous = null,
    ) {
        parent::__construct($message, 501, $errorCode, $previous);
    }
}
