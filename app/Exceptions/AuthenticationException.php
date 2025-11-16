<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use Throwable;

class AuthenticationException extends ClientApiException
{
    public function __construct(
        string $message = 'Invalid API key or access token (unrecognized login or wrong password).',
        Throwable $previous = null,
    ) {
        parent::__construct($message, 401, ApiExceptionErrorCodes::INVALID_LOGIN, $previous);
    }
}
