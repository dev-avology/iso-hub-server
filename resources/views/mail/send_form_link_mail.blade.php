<!DOCTYPE html>
<html>
<head>
    <title>ISO Form Submission</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #333;">Hello {{ $data['email'] ?? '' }},</h2>

        <p>ISO Form Submission Link</p>

        <p><strong>DBA:</strong> {{ $data['dba'] }}</p>
        <p><strong>Merchant Name:</strong> {{ $data['merchant_name'] }}</p>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
        <p><strong>ISO Form URL:</strong> {{ $data['iso_form_link'] }}</p>

        {{-- <p>Please change your password after logging in for security purposes.</p> --}}

        <p style="margin-top: 30px;">
            <a href="{{ $data['iso_form_link'] }}" target="_blank" style="background-color: #1d72b8; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">View ISO Form</a>
        </p>

        <p style="margin-top: 40px;">Best regards,<br>
        The ISO HUB Team</p>
    </div>
</body>
</html>

