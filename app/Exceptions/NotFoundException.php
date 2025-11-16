<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use Throwable;

class NotFoundException extends ClientApiException
{
    public function __construct(
        string $message = 'Resource not found.',
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::RESOURCE_NOT_FOUND,
        Throwable $previous = null,
    ) {
        parent::__construct($message, 404, $errorCode, $previous);
    }
}
