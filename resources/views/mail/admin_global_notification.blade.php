<!DOCTYPE html>
<html>
<head>
    <title>Birthday Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div style="text-align: center;">
            <h1 style="color: #ff6b6b;">🎂 Happy Birthday!</h1>
            <p style="font-size: 18px; color: #333;">Hello {{ $user->first_name ?? 'Valued User' }},</p>
        </div>

        <div style="margin-top: 20px; font-size: 16px; color: #555;">
            <p>{{ $msg ?? '' }}</p>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <img src="https://cdn-icons-png.flaticon.com/512/4389/4389342.png" alt="Cake" width="100" style="margin-bottom: 20px;">
            <p style="color: #888;">Wishing you a fantastic year ahead!</p>
        </div>

        <div style="margin-top: 40px; text-align: center; font-size: 14px; color: #aaa;">
            <p>— ISO HUB Team</p>
        </div>
    </div>
</body>
</html>
