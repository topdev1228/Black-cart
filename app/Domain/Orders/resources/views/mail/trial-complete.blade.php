<x-mail::message :layoutContent="$layoutContent">
    <table class="trial-complete-content">
        <tr class="body-content">
            <td>
                {{ __('Hey :name, your trial has ended!', ['name' => $firstName]) }}
                <br /><br />
                {{ __('We hope that you had a great Try Before You Buy experience. Your original payment method will be charged for any items kept. If you have started a return for any items, you will not be charged for these. Please follow the return instructions you received as part of the return.') }}
            </td>
        </tr>
        <tr>
            <td>
                @include('vendor.mail.html.line-items-purchase', [
                    'lineItems' => $order->lineItems
                ])
            </td>
        </tr>
    </table>
    @include('vendor.mail.html.footer-cart', [
        'text' => "We hope you have a great Try Before You Buy experience with Blackcart!"
    ])
</x-mail::message>
