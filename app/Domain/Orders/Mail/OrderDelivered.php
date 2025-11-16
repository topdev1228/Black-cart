<?php
declare(strict_types=1);

namespace App\Domain\Orders\Mail;

use App;
use App\Domain\Orders\Values\Order;
use App\Domain\Shared\Mail\BaseMail;
use App\Domain\Shared\Values\MailLayoutContent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

class OrderDelivered extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected MailLayoutContent $layoutContent;
    protected string $orderNumber;

    public string $merchantName;

    public function __construct(protected Order $order, protected int $tryDays, protected string $returnUrl)
    {
        parent::__construct();

        $store = App::context()->store;

        $this->orderNumber = $this->order->orderName();
        $this->merchantName = $store->name;

        $this->layoutContent = new MailLayoutContent(
            title: __('Try your items for :numDays days', [
                'numDays' => $this->tryDays,
            ]),
            headerIcon: 'try.png',
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
            subject: __('Your :merchantName order :orderNumber trial has started', [
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
            markdown: 'Orders::mail.order-delivered',
            with: [
                'layoutContent' => $this->layoutContent,
                'order' => $this->order->hydrateLineItems(),
                'firstName' => $this->order->customerFirstName(),
                'tryDays' => $this->tryDays,
                'trialEnd' => Date::now()->addDays($this->tryDays)->format('l, F jS'),
                'returnUrl' => $this->returnUrl,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
