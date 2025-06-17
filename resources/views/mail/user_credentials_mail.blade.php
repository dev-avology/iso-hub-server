<!DOCTYPE html>
<html>
<head>
    <title>User Credentials - ISO HUB</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #333;">Hello {{ $data['name'] ?? 'User' }},</h2>

        <p>You have been registered on <strong>ISO HUB</strong>. Here are your credentials:</p>

        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        <p><strong>Password:</strong> {{ $data['password'] }}</p>

        <p>Please change your password after logging in for security purposes.</p>

        <p style="margin-top: 30px;">
            <a href="{{ $data['login_url'] }}" style="background-color: #1d72b8; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Login to ISO HUB</a>
        </p>

        <p style="margin-top: 40px;">Best regards,<br>
        The ISO HUB Team</p>
    </div>
</body>
</html>
