<?php
declare(strict_types=1);

namespace App\Enums\Exceptions;

enum ApiExceptionErrorCodes: string
{
    // 400
    case INVALID_REQUEST = 'invalid_request';

    // 401
    case INVALID_LOGIN = 'invalid_login';

    // 422
    case INVALID_PARAMETERS = 'invalid_parameters';

    // 404
    case RESOURCE_NOT_FOUND = 'resource_not_found';
    // RESOURCE_NOT_FOUND is a default value that shouldn't be used
    // We should use the more specific *_NOT_FOUND enums below
    case STORE_NOT_FOUND = 'store_not_found';
    case STORE_SETTING_NOT_FOUND = 'store_setting_not_found';
    case PROGRAM_NOT_FOUND = 'program_not_found';

    // 500
    case SERVER_ERROR = 'server_error';

    // 501
    case NOT_IMPLEMENTED = 'not_implemented';
}
