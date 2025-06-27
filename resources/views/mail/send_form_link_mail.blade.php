<!DOCTYPE html>
<html>
<head>
    <title>CoCard Form Submission</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #333;">Hello {{ $data['email'] ?? '' }},</h2>

       <img src="{{ url('/api/track-email-open/'.$data['form_id']) }}" width="1" height="1" style="display: none;" alt="." />

        <p>CoCard Form Submission Link</p>

        @php
            $separator = Str::contains($data['iso_form_link'], '?') ? '&' : '?';
            $safeUrl = $data['iso_form_link'] . $separator . 'id=' . $data['form_id'];
        @endphp

        <p><strong>DBA:</strong> {{ $data['dba'] }}</p>
        <p><strong>Merchant Name:</strong> {{ $data['merchant_name'] }}</p>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
        <p><strong>CoCard Form URL:</strong> {{ $safeUrl  }}</p>

        {{-- <p>Please change your password after logging in for security purposes.</p> --}}

        @php
            $encodedUrl = urlencode(base64_encode($data['iso_form_link']));
        @endphp

        <p style="margin-top: 30px;">
            <a href="{{ url('api/redirect-to-iso-form/'.$data['form_id'].'/'.$encodedUrl) }}" target="_blank" style="background-color: #1d72b8; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">View CoCard Form</a>
        </p>

        <p style="margin-top: 40px;">Best regards,<br>
        The CoCard Team</p>
    </div>
</body>
</html>

