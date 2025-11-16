<x-mail::message :layoutContent="$layoutContent">
    <table class="assumed-delivery-merchant-reminder-content" role="presentation" aria-describedby="No fulfillment notification received">
        <tr class="body-content">
            <td>
                {{ __('Dear :firstName', ['firstName' => $firstName]) }}
                <br /><br />
                {{__('There has not been a delivery notification of any TBYB line items in this order.')}}
                <br /><br />
                {{__('It has been 8 days since the order was placed. Please review the shipping tracking information and update if incorrect. It may also be an issue with the delivery carrier’s system. We will automatically start the trial in 2 more days if no delivery notification is recorded in the Shopify order.')}}
                <br /><br />
                {{__('Thank you for your prompt attention.')}}
                <br /><br />
                <b>{{__('If you have a question, please reach out at merchantsupport@blackcart.com.')}}</b>
                <br /><br />
                <i>{{__('The Blackcart Team!')}}</i>
            </td>
        </tr>
    </table>
    @include('vendor.mail.html.footer-cart', [
        'text' => "Let's fill your Blackcart with orders!"
    ])
</x-mail::message>
