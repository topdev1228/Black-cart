<x-mail::message :layoutContent="$layoutContent">
    <table class="re-auth-failed-content">
        <tr class="body-content">
            <td>
                {{ __('Hey :name', ['name' => $firstName]) }}
                <br /><br />
                {{ __('The authorization we have on your card expires on :authExpiry, so we tried to re-authorize your card for :authAmount. Unfortunately, the re-authorization failed. This can happen if the available credit on your card has changed since the order was placed.', ['authExpiry' => $authExpiry, 'authAmount' => $authAmount]) }}
                <br /><br />
                {{__('Since we couldn\'t re-authorize, we had to charge your card for the outstanding balance.')}}
                <br /><br />
                {{__('Don\'t worry, you can still return any items you don\'t want to keep within :merchantName return policy. Once your returns have been received, a refund will be issued to your card.', ['merchantName' => $layoutContent->companyName])}}
            </td>
        </tr>
    </table>
    @include('vendor.mail.html.footer-cart', [
        'text' => "We hope you have a great Try Before You Buy experience with Blackcart!"
    ])
</x-mail::message>
