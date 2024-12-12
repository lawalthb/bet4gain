@component('mail::message')
# Admin Login OTP

Your OTP for admin login is: **{{ $otp }}**

This OTP will expire in 10 minutes.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
