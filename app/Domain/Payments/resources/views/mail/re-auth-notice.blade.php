<x-mail::message :layoutContent="$layoutContent">
    <table class="re-auth-failed-content">
        <tr class="body-content">
            <td>
                {{ __('Hey :name', ['name' => $firstName]) }}
                <br /><br />
                {{ __('The authorization on your payment method will expire on :authExpiry.', ['authExpiry' => $authExpiry]) }}
                <br /><br />
                {{ __('Your trial period is ongoing and as a reminder, your trial end date is :authExpiry.', ['authExpiry' => $authExpiry]) }}
                <br /><br />
                {{ __('So we need to re-authorize your card for :authAmount', ['authAmount' => $authAmount]) }}
                <br /><br />
                {{__('Don\'t worry, you still won\'t be charged until you decide what to keep. Your existing authorization will disappear from your statement in a few business days.')}}
            </td>
        </tr>
    </table>
    @include('vendor.mail.html.footer-cart', [
        'text' => "We hope you have a great Try Before You Buy experience with Blackcart!"
    ])
</x-mail::message>
