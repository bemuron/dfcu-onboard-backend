@component('mail::message')
<h1> <img src="{{ asset('assets/img/email-header.jpg') }}" alt="" class="img-fluid"></h1><br>

<H2>Dear, {{ $username }}</H2>

<p style="font-size: 17px;">
    {{ $message }}
</p>

Thank you for choosing FixApp,<br>

Best Regards,<br>

The FixApp Team
@endcomponent