<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Enums\Exceptions\ApiExceptionTypes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

/**
 * @param int<100,599> $code
 */
class ApiException extends Exception
{
    public function __construct(
        string $message,
        int $code,
        protected ApiExceptionTypes $type,
        protected ApiExceptionErrorCodes $errorCode,
        Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response
    {
        return response(
            [
                'type' => $this->type,
                'code' => $this->errorCode,
                'message' => $this->getMessage(),
                'errors' => [],
            ],
            $this->getCode(),
        );
    }
}
