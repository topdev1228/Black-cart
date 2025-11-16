<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $fileUrl
 */
class ReverseDeliveryLabelInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $fileUrl
     */
    public static function make($fileUrl): self
    {
        $instance = new self;

        if ($fileUrl !== self::UNDEFINED) {
            $instance->fileUrl = $fileUrl;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fileUrl' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
