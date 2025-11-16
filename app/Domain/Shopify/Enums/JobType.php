<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Enums;

enum JobType: string
{
    case MUTATION = 'mutation';
    case QUERY = 'query';
}
