<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ISO Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            padding: 40px 20px;
        }
        .email-box {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }
        .email-box h2 {
            color: #000;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .email-box p {
            margin: 6px 0;
            font-size: 14px;
        }
        .highlight-link {
            font-weight: bold;
            font-size: 14px;
            color: #dc3545;
            margin-top: 20px;
        }
        .iso-link-button {
            display: inline-block;
            margin-top: 15px;
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }
        .footer-message {
            margin-top: 40px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="email-box">
        <h2>ISO Form Link</h2>
        <p><strong>DBA:</strong> {{ $data['dba'] }}</p>
        <p><strong>Merchant Name:</strong> {{ $data['merchant_name'] }}</p>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        <p><strong>Phone:</strong> {{ $data['phone'] }}</p>

        <p class="highlight-link">ISO Form URL: {{ $data['iso_form_link'] }}</p>

        <a href="{{ $data['iso_form_link'] }}" class="iso-link-button" target="_blank">
            View ISO Form
        </a>

        <p class="footer-message">
            If you have any questions, feel free to reach out.<br>
            Best regards,<br>
            <strong style="color:#000;">ISO HUB</strong>
        </p>
    </div>
</body>
</html>
