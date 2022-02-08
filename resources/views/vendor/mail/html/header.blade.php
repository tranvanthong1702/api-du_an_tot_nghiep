<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://i.pinimg.com/564x/83/b7/ad/83b7ad65a07dd75181dece8a70d95404.jpg" class="logo" alt="MarkVeget">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
