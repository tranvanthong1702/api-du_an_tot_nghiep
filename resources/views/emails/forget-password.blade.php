@component('mail::message')

<div>Xin chào <b style="text-transform: capitalize;">{{$data['name']}}</b>.</div>
<div>Mật khẩu mới của bạn là <b>{{$data['password']}}</b></div>
<br>

@component('mail::button', ['url' => $data['url'],'color' => 'success'])
Login
@endcomponent

Thanks,<br>
MarkVeget.com.vn
@endcomponent
