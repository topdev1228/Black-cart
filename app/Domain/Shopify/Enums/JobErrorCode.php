<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Enums;

enum JobErrorCode: string
{
    // Query
    case ACCESS_DENIED = 'access_denied';
    case INTERNAL_SERVER_ERROR = 'internal_server_error';
    case TIMEOUT = 'timeout';

    // Mutation
    case INTERNAL_FILE_SERVER_ERROR = 'internal_file_server_error';
    case INVALID_MUTATION = 'invalid_mutation';
    case INVALID_STAGED_UPLOAD_FILE = 'invalid_staged_upload_file';
    case NO_SUCH_FILE = 'no_such_file';
    case OPERATION_IN_PROGRESS = 'operation_in_progress';
}
