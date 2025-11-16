<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $email
 * @property string|null $firstName
 * @property string|null $lastName
 * @property string|null $locale
 * @property string|null $phone
 * @property string|null $title
 */
class CompanyContactInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $locale
     * @param string|null $phone
     * @param string|null $title
     */
    public static function make(
        $email = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $firstName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $lastName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locale = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $phone = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($email !== self::UNDEFINED) {
            $instance->email = $email;
        }
        if ($firstName !== self::UNDEFINED) {
            $instance->firstName = $firstName;
        }
        if ($lastName !== self::UNDEFINED) {
            $instance->lastName = $lastName;
        }
        if ($locale !== self::UNDEFINED) {
            $instance->locale = $locale;
        }
        if ($phone !== self::UNDEFINED) {
            $instance->phone = $phone;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'email' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'firstName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'lastName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'locale' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'phone' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
