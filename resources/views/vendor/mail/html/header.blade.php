@props(['layoutContent'])
<style>
    .help-content td a, table.header-block tr.order-number td  {
        color: {{ $layoutContent->blackcartOrderNumberColor }};
    }
    .footer-wrapper  {
        background: {{ $layoutContent->blackcartLinearGradientFooter }};
    }
    .header {
        background: {{ $layoutContent->blackcartLinearGradientHeader }};
    }
</style>
<tr>
<td class="header">
<a href="{{ url(App::context()->store?->domain ?? config('APP_URL'), [], true) }}" style="display: inline-block;">
    <table class="header-block">
        <tr class="blackcart-logo"><td><img src="https://storage.googleapis.com/origin-static.blackcart.co/assets/bc-logo.png"></td></tr>
        <tr class="email-icon"><td><img src="https://static.blackcart.com/email/shopify-app/{{ $layoutContent?->headerIcon ?? 'shop.png' }}" alt="Email Icon"/></td></tr>
        <tr class="title"><td>
                @if ($layoutContent?->title)
                    {{ $layoutContent?->title }}
                    @if ($layoutContent?->titleLine2)
                        <br />
                        {{ $layoutContent?->titleLine2 }}
                    @endif
                @else
                    {{ __('Your Blackcart Order') }}
                @endif
        </td></tr>
        <tr class="order-number"><td>
            @if ($layoutContent?->orderNumber)
            {{ __('Your order number is') }}: {{ $layoutContent->orderNumber }}
            @endif
        </td></tr>
    </table>
</a>
</td>
</tr>
