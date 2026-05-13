<x-mail::message>
# Email verification

Hello {{ $recipientName }},

Use the following one-time code to verify your email address:

<x-mail::panel>
**{{ $otp }}**
</x-mail::panel>

This code expires in 10 minutes.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
