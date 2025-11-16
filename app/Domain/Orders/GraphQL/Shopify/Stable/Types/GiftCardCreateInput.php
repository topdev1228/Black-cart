<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $initialValue
 * @property string|null $code
 * @property int|string|null $customerId
 * @property mixed|null $expiresOn
 * @property string|null $note
 * @property bool|null $notify
 * @property string|null $templateSuffix
 */
class GiftCardCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $initialValue
     * @param string|null $code
     * @param int|string|null $customerId
     * @param mixed|null $expiresOn
     * @param string|null $note
     * @param bool|null $notify
     * @param string|null $templateSuffix
     */
    public static function make(
        $initialValue,
        $code = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $expiresOn = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $notify = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $templateSuffix = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($initialValue !== self::UNDEFINED) {
            $instance->initialValue = $initialValue;
        }
        if ($code !== self::UNDEFINED) {
            $instance->code = $code;
        }
        if ($customerId !== self::UNDEFINED) {
            $instance->customerId = $customerId;
        }
        if ($expiresOn !== self::UNDEFINED) {
            $instance->expiresOn = $expiresOn;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($notify !== self::UNDEFINED) {
            $instance->notify = $notify;
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
            'initialValue' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'code' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'customerId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'expiresOn' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'notify' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
