<table class="line-item-list">
    <tr class="title-row"><td>Your Try Before You Buy Items</td></tr>
    <tr class="line-item-container">
        <td>
            <table class="line-items">
                @foreach($lineItems as $lineItem)
                    @if (!$lineItem->isTbyb)
                        @continue
                    @endif
                    <tr>
                        <td>
                            <table style="width: 100%; margin: 15px 0;">
                                <tr>
                                    <td class="line-item-image"><!-- img --></td>
                                    <td class="line-item-info">
                                        <span class="item-title">{{ $lineItem->productTitle }}</span>
                                        <span class="item-subtitle">{{ $lineItem->variantTitle }}</span>
                                    </td>
                                    <td class="line-item-price">
                                        <span class="item-deposit">{{ $lineItem->isTbyb && !empty($lineItem->depositCustomerAmount) ? $lineItem->depositCustomerAmount->formatTo('en_US', true) . ' deposit' : '' }}</span>
                                        <span class="item-keep-price">{{ $lineItem->isTbyb ? '('.$lineItem->priceCustomerAmount->formatTo('en_US', true).' if kept)'  : '' }}</span>
                                        <span class="item-paid-price">{{ $lineItem->isTbyb ? ''  : $lineItem->priceCustomerAmount->formatTo('en_US', true) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>

</table>
