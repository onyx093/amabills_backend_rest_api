@component('mail::message')
# Password reset request

You requested to reset your password. To complete this request, Click on the button below

@component('mail::button', ['url' => 'http://localhost:8000/reset/' . $data["token"]])
Reset password now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
