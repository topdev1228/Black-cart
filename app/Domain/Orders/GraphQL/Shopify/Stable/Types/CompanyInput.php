<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $customerSince
 * @property string|null $externalId
 * @property string|null $name
 * @property string|null $note
 */
class CompanyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $customerSince
     * @param string|null $externalId
     * @param string|null $name
     * @param string|null $note
     */
    public static function make(
        $customerSince = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $externalId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customerSince !== self::UNDEFINED) {
            $instance->customerSince = $customerSince;
        }
        if ($externalId !== self::UNDEFINED) {
            $instance->externalId = $externalId;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerSince' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'externalId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
