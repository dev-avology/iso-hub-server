<!DOCTYPE html>
<html>
<head>
    <title>New Notification from ISO HUB</title>
</head>
<body>
    <p>Hello {{ $data->first_name ?? '' }},</p>

    <p>You have a new notification on <strong>ISO HUB</strong>.</p>

    <p><strong>{{ $msg ?? "" }}</strong></p>

    <p>Best regards,<br>
    The ISO HUB Team</p>
</body>
</html>
