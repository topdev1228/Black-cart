<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $customerId
 * @property mixed|null $expiresOn
 * @property string|null $note
 * @property string|null $templateSuffix
 */
class GiftCardUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $customerId
     * @param mixed|null $expiresOn
     * @param string|null $note
     * @param string|null $templateSuffix
     */
    public static function make(
        $customerId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $expiresOn = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $templateSuffix = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customerId !== self::UNDEFINED) {
            $instance->customerId = $customerId;
        }
        if ($expiresOn !== self::UNDEFINED) {
            $instance->expiresOn = $expiresOn;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($templateSuffix !== self::UNDEFINED) {
            $instance->templateSuffix = $templateSuffix;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'expiresOn' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'templateSuffix' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
