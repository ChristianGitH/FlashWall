@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; color: #af4bff; text-decoration: none;">
<table align="center" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto; text-align: center;">
<tr>
<td style="vertical-align: middle; padding-right: 8px;">
<img src="{{ asset('favicon-96x96.png') }}" alt="" width="32" height="32" style="display: block; width: 32px; height: 32px; border: 0;">
</td>
<td style="vertical-align: middle; font-family: Figtree, Arial, sans-serif; font-size: 30px; line-height: 32px; font-weight: 700; color: #af4bff;">
<span style="color: #af4bff; background: linear-gradient(90deg, #a855f7 0%, #f9a8d4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ config('app.name') }}</span>
</td>
</tr>
</table>
</a>
</td>
</tr>
