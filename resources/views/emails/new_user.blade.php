@component('mail::message')
<h1> <img src="{{ asset('assets/img/logo.png') }}" alt="" class="img-fluid"></h1><br>

<p style="font-size: 17px;">
    To verify your email address in the DFCU Onboarding app, enter the following code:
</p>

<h3>{{ $code }}</h3>

<p style="font-size: 17px;">
    This code expires after 5 minutes.
</p>

<p style="font-size: 17px;">
    If you didn't request this email, you can safely ignore it.
</p>
@endcomponent