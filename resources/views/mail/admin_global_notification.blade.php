<!DOCTYPE html>
<html>
<head>
    <title>Admin Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #333;">Hello {{ $user->first_name ?? '' .' '. $user->last_name ?? '' }},</h2>

        <p>{{ $msg }}</p>

        <p style="margin-top: 40px;">Best regards,<br>
        The ISO HUB Team</p>
    </div>
</body>
</html>
