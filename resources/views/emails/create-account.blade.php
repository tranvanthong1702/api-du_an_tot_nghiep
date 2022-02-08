@component('mail::message')

<div>Xin chào <b style="text-transform: capitalize;">{{$data['name']}}</b>.</div>
<div>Chúng tôi đã tạo cho bạn một tài khoản tại MarkVeget.</div>
<div>Mật khẩu đăng nhập của bạn là <b>{{$data['password']}}</b></div>
<br>

@component('mail::button', ['url' => $data['url'],'color' => 'success'])
Login
@endcomponent

Thanks,<br>
MarkVeget.com.vn
@endcomponent
