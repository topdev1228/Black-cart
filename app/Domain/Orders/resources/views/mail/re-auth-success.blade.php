<x-mail::message :layoutContent="$layoutContent">
    <table class="re-auth-failed-content">
        <tr class="body-content">
            <td>
                {{ __('Hey :name', ['name' => $firstName]) }}
                <br /><br />
                {{ __('Your card has be re-authorized for :authAmount.', ['authAmount' => $authAmount]) }}
                <br /><br />
                {{__('Don\'t worry, you still won\'t be charged until you decide what to keep. The previous authorization will disappear from your statement in a few business days.')}}
            </td>
        </tr>
    </table>
    @include('vendor.mail.html.footer-cart', [
        'text' => "We hope you have a great Try Before You Buy experience with Blackcart!"
    ])
</x-mail::message>
