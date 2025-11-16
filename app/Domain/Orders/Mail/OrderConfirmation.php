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

class OrderConfirmation extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected MailLayoutContent $layoutContent;
    protected string $orderNumber;
    public string $merchantName;

    /**
     * Create a new message instance.
     */
    public function __construct(protected OrderValue $order, protected string $returnUrl)
    {
        parent::__construct();

        $store = App::context()->store;
        $this->orderNumber = $this->order->orderName();
        $this->merchantName = $store->name;

        $this->layoutContent = new MailLayoutContent(
            title: __('Try Before You Buy'),
            headerIcon: 'order.png',
            titleLine2: __('Order Confirmation'),
            orderNumber: $this->orderNumber,
            companyName: $store->name,
            companyContact: $this->storeEmail,
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your :merchantName order :orderNumber is confirmed', [
                'orderNumber' => $this->orderNumber,
                'merchantName' => $this->merchantName,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'Orders::mail.order-confirmation',
            with: [
                'layoutContent' => $this->layoutContent,

                'order' => $this->order->hydrateLineItems(),
                'firstName' => $this->order->customerFirstName(),
                'returnUrl' => $this->returnUrl,
//                'endLink' => route('orders.web.view.orders.end-early', [
//                    'id' => $this->order->id,
//                ]),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
