<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<string>|null $bcc
 * @property string|null $body
 * @property string|null $customMessage
 * @property string|null $from
 * @property string|null $subject
 * @property string|null $to
 */
class EmailInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<string>|null $bcc
     * @param string|null $body
     * @param string|null $customMessage
     * @param string|null $from
     * @param string|null $subject
     * @param string|null $to
     */
    public static function make(
        $bcc = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $body = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customMessage = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $from = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $subject = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $to = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($bcc !== self::UNDEFINED) {
            $instance->bcc = $bcc;
        }
        if ($body !== self::UNDEFINED) {
            $instance->body = $body;
        }
        if ($customMessage !== self::UNDEFINED) {
            $instance->customMessage = $customMessage;
        }
        if ($from !== self::UNDEFINED) {
            $instance->from = $from;
        }
        if ($subject !== self::UNDEFINED) {
            $instance->subject = $subject;
        }
        if ($to !== self::UNDEFINED) {
            $instance->to = $to;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'bcc' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'body' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'customMessage' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'from' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'subject' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'to' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
