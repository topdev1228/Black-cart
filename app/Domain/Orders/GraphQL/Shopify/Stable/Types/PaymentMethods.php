<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PaymentMethods
{
    public const AMERICAN_EXPRESS = 'AMERICAN_EXPRESS';
    public const BITCOIN = 'BITCOIN';
    public const BOGUS = 'BOGUS';
    public const DANKORT = 'DANKORT';
    public const DINERS_CLUB = 'DINERS_CLUB';
    public const DISCOVER = 'DISCOVER';
    public const DOGECOIN = 'DOGECOIN';
    public const EFTPOS = 'EFTPOS';
    public const ELO = 'ELO';
    public const FORBRUGSFORENINGEN = 'FORBRUGSFORENINGEN';
    public const INTERAC = 'INTERAC';
    public const JCB = 'JCB';
    public const LITECOIN = 'LITECOIN';
    public const MAESTRO = 'MAESTRO';
    public const MASTERCARD = 'MASTERCARD';
    public const PAYPAL = 'PAYPAL';
    public const UNIONPAY = 'UNIONPAY';
    public const VISA = 'VISA';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
