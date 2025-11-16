<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Enums\Exceptions\ApiExceptionTypes;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @param int<500,599> $code
 */
class ServerApiException extends ApiException
{
    public function __construct(
        string $message = 'Something went wrong, please try again in a few minutes.',
        int $code = 500,
        ApiExceptionErrorCodes $errorCode = ApiExceptionErrorCodes::SERVER_ERROR,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, ApiExceptionTypes::API_ERROR, $errorCode, $previous);
    }

    public function report(): void
    {
        if ($this->getPrevious() !== null) {
            Log::error($this->getPrevious()->getMessage());
        } else {
            Log::error($this->getMessage());
        }
    }
}
