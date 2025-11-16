<x-mail::message :layoutContent="$layoutContent">
<table class="order-confirmation-content">
    <tr class="body-content">
        <td>
            {{ __('Hey :name', ['name' => $firstName]) }}
            <br /><br />
            {{ __('When your items are delivered, your trial will begin.') }}
            <br />
            {{ __('Decide whether you want to keep or send them back.') }}
            <br />
            {{ __('Once the try period is completed, your card will be automatically charged for all Try Before You Buy items.') }}
        </td>
    </tr>
    <tr>
        <td>
            @include('vendor.mail.html.how-it-works')
        </td>
    </tr>
    <tr>
        <td>
            @include('vendor.mail.html.line-items', [
                'lineItems' => $order->lineItems
            ])
        </td>
    </tr>
</table>

@include('vendor.mail.html.footer-cart', [
    'text' => "We hope you have a great Try Before You Buy experience with Blackcart!"
])
</x-mail::message>
