<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DisputeEvidenceUpdateUserErrorCode
{
    public const DISPUTE_EVIDENCE_NOT_FOUND = 'DISPUTE_EVIDENCE_NOT_FOUND';
    public const EVIDENCE_ALREADY_ACCEPTED = 'EVIDENCE_ALREADY_ACCEPTED';
    public const EVIDENCE_PAST_DUE_DATE = 'EVIDENCE_PAST_DUE_DATE';
    public const FILES_SIZE_EXCEEDED_LIMIT = 'FILES_SIZE_EXCEEDED_LIMIT';
    public const INVALID = 'INVALID';
    public const TOO_LARGE = 'TOO_LARGE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
