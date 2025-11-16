<?php
declare(strict_types=1);

namespace App\Enums\Exceptions;

enum ApiExceptionTypes: string
{
    case API_ERROR = 'api_error';
    case REQUEST_ERROR = 'request_error';
}
