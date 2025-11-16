<x-mail::message :layoutContent="$layoutContent">
    <table class="re-auth-failed-content">
        <tr class="body-content">
            <td>
                {{ __('Hey :name', ['name' => $firstName]) }}
                <br /><br />
                {{ __('The authorization attempted on your payment method failed, so we\'ve canceled your order. The authorization is verification from the card issuer that funds are available to cover the order.') }}
                <br /><br />
                {{ __('You can re-order with :merchantName with a different payment method that has enough funds availability for the order.', ['merchantName' => $layoutContent->companyName]) }}
            </td>
        </tr>
    </table>
    @include('vendor.mail.html.footer-cart', [
        'text' => __('Sorry it didn\'t work out this time. We hope you can enjoy the Blackcart Experience soon.'),
    ])
</x-mail::message>
