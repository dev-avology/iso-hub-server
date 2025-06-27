<!DOCTYPE html>
<html>
<head>
    <title>New Notification from CoCard</title>
</head>
<body>
    <p>Hello {{ $data['name'] ?? '' }},</p>

    <p>You have a new notification on <strong>CoCard</strong>.</p>

    <p><strong>{{ $data['message'] ?? "" }}</strong></p>

    <p>Best regards,<br>
    The CoCard Team</p>
</body>
</html>
