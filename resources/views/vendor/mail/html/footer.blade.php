@props(['layoutContent'])
<tr class="footer-wrapper">
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr class="help-title">
    <td>
        Have Questions?
    </td>
</tr>
@if(!empty($layoutContent))
<tr class="help-content">
    <td>
        For any questions, please email {{ $layoutContent->companyName }} at <a href="mailto:{{ $layoutContent->companyContact }}">{{ $layoutContent->companyContact }}</a>
    </td>
</tr>
@else
    <tr class="help-content">
        <td>
            For any questions, please reach out to the store.</a>
        </td>
    </tr>
@endif
<tr>
<td class="content-cell footer-copyright" align="center">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
</table>
</td>
</tr>
