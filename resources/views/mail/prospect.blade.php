<!DOCTYPE html>
<html>
<head>
    <title>Welcome to CoCard</title>
</head>
<body>
    <p>Hello, {{$name}}</p>
    <p>We requested you to upload documents. Please click the link below to get started:</p>
    <p><a href="{{ $encryptedLink }}" style="display:inline-block; padding:10px 20px; color:#fff; background:#007bff; text-decoration:none; border-radius:5px;">Upload Documents</a></p>
    <p>If you have any questions, feel free to reach out.</p>
    <p>Best regards,<br><strong style="color:#000;">CoCard</strong></p>
</body>
</html>

