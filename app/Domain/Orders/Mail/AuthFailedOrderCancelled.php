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

class AuthFailedOrderCancelled extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected MailLayoutContent $layoutContent;
    protected string $orderNumber;
    public string $merchantName;

    /**
     * Create a new message instance.
     */
    public function __construct(protected OrderValue $order)
    {
        parent::__construct();

        $store = App::context()->store;
        $this->merchantName = $store->name;
        $this->orderNumber = $this->order->orderName();

        $this->layoutContent = new MailLayoutContent(
            title: __('Your order was cancelled because payment authorization failed'),
            headerIcon: 'failed.png',
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
            subject: __('Your :merchantName order :orderNumber was canceled', [
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
            markdown: 'Orders::mail.order-cancel-auth-failed',
            with: [
                'layoutContent' => $this->layoutContent,
                'order' => $this->order,
                'firstName' => $this->order->customerFirstName(),
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
