@php use App\Domain\Orders\Enums\LineItemDecisionStatus; @endphp
<table class="line-item-list">
    <tr class="title-row">
        <td>Your Try Before You Buy Items</td>
    </tr>
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
                                        <span class="item-price-container">
                                            <span class="item-paid-price">{{ $lineItem->priceCustomerAmount->formatTo('en_US', true) }}</span>
                                            @if ($lineItem->decisionStatus == LineItemDecisionStatus::KEPT)
                                                <span class="kept-tag">KEPT</span>
                                            @elseif ($lineItem->decisionStatus == LineItemDecisionStatus::RETURNED)
                                                <span class="returned-tag">RETURNED</span>
                                            @endif
                                        </span>
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
