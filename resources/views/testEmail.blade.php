@component('mail::message')
# Introduction

The body of your message.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

@component('mail::panel')
This is the panel content.
@endcomponent
@component('mail::table')
<table>
    
</table>
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent
