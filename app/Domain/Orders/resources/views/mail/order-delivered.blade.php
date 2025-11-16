<x-mail::message :layoutContent="$layoutContent">
<table class="order-delivered-content">
    <tr class="end-timer">
        <td>
            <span>{{ __('Ends: ') }} {{ $trialEnd }}</span>
        </td>
    </tr>
    <tr class="body-content">
        <td>
            {{ __('Hey :name, your :tryDays day trial has begun!', [
                'name' => $firstName,
                'tryDays' => $tryDays
            ]) }} <br /><br />
            {{ __("Now that your items have been delivered, it's time to try your items at home! Once the trial period is complete, your payment method will be automatically charged for all items that you've decided to keep.") }}
        </td>
    </tr>
    <tr>
        <td>
            @include('vendor.mail.html.line-items', [
                'lineItems' => $order->lineItems
            ])
        </td>
    </tr>
    <tr class="returns">
        <td>
            <table>
                <tr><td>{{ __('If you want to return any of your items before the trial period ends, click the link below.') }} </td></tr>
                <tr><td><a href="{{ $returnUrl }}" rel="noopener noreferrer nofollow">{{ __('Start Return') }}</a></td></tr>
            </table>
        </td>
    </tr>
</table>

@include('vendor.mail.html.footer-cart', [
    'text' => "We hope you had a great experience"
])
</x-mail::message>
