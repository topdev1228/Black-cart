<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values;

/**
 * This class represents some common (but not necessarily required) values for all of our customer emails
 *
 * This value can be instantiated in the constructor of your Mailable and passed as 'layoutContent' to the view
 *
 * The hope is to prevent an issue we had in previous emails where common, header/footer variables were copied and pasted
 *  from email to email, which made maintaining them more complicated.
 */
class MailLayoutContent extends Value
{
    public function __construct(
        public string $title = 'Your Blackcart Order',
        public string $headerIcon = 'shop.png',
        public ?string $titleLine2 = null, // optional second title line to break up the title for better display
        public ?string $orderNumber = null,
        public ?string $companyName = null,
        public ?string $companyContact = null,
        public string $blackcartOrderNumberColor = '#007DC1;',
        public string $blackcartLinearGradientHeader = 'background: linear-gradient(#FFFFFF, #EBF6FF);',
        public string $blackcartLinearGradientFooter = 'background: linear-gradient(#FFFFFF, #EBF6FF);',
    ) {
    }
}
