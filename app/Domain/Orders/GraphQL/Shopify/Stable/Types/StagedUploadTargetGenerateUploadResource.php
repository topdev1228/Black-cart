<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class StagedUploadTargetGenerateUploadResource
{
    public const BULK_MUTATION_VARIABLES = 'BULK_MUTATION_VARIABLES';
    public const COLLECTION_IMAGE = 'COLLECTION_IMAGE';
    public const FILE = 'FILE';
    public const IMAGE = 'IMAGE';
    public const MODEL_3D = 'MODEL_3D';
    public const PRODUCT_IMAGE = 'PRODUCT_IMAGE';
    public const RETURN_LABEL = 'RETURN_LABEL';
    public const SHOP_IMAGE = 'SHOP_IMAGE';
    public const URL_REDIRECT_IMPORT = 'URL_REDIRECT_IMPORT';
    public const VIDEO = 'VIDEO';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
