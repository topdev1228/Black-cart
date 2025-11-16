<?php
declare(strict_types=1);

namespace App\Domain\Orders\Mail;

use App;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Shared\Mail\BaseMail;
use App\Domain\Shared\Values\MailLayoutContent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssumedDeliveryMerchantReminder extends BaseMail
{
    use Queueable;
    use SerializesModels;

    public MailLayoutContent $layoutContent;
    public string $orderNumber;
    public $store;

    public function __construct(protected OrderValue $order, protected string $merchantEmail)
    {
        parent::__construct();

        $this->orderNumber = $this->order->orderName();
        $this->store = App::context()->store;

        $this->layoutContent = new MailLayoutContent(
            title: __('No delivery notification recorded'),
            headerIcon: 'green-exclamation.png',
            orderNumber: $this->orderNumber,
            companyName: $this->store->name,
            companyContact: $this->storeEmail,
            blackcartOrderNumberColor: '#22B573;',
            blackcartLinearGradientHeader: 'linear-gradient(180deg, #78FFC2 0%, #C0FFE3 0.01%, #FFF 100%);',
            blackcartLinearGradientFooter: 'background: linear-gradient(180deg, #78FFC2 0%, #C0FFE3 0.01%, #FFF 100%);',
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('No delivery notification for Order :orderNumber', [
                'orderNumber' => $this->orderNumber,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'Orders::mail.assumed-delivery-merchant-reminder',
            with: [
                'layoutContent' => $this->layoutContent,
                'firstName' => $this->store->name,
            ]
        );
    }
}
