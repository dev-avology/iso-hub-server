<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ISO Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
            color: #333;
            padding: 20px;
        }
        .email-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            max-width: 600px;
            margin: auto;
        }
        .email-box h2 {
            color: #ffc107; /* Yellow */
            margin-bottom: 20px;
        }
        .email-box p {
            margin: 6px 0;
        }
        .highlight-link {
            font-weight: bold;
            font-size: 16px;
            color: #dc3545; /* red */
            margin-top: 20px;
        }
        .iso-link-button {
            display: inline-block;
            margin-top: 10px;
            background-color: #ffc107; /* Yellow */
            color: #000;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
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
