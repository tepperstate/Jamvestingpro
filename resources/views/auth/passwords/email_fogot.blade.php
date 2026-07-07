
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0078D7;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 20px;
        }
        .content h1 {
            font-size: 22px;
            margin: 0 0 10px;
        }
        .content p {
            font-size: 16px;
            margin: 0 0 15px;
            line-height: 1.5;
        }
        .content a {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #0078D7;
            text-decoration: none;
            border-radius: 5px;
        }
        .content a:hover {
            background-color: #0056a3;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777;
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="" alt="Morrison Markets Logo">
        </div>
        <div class="content">
            <h1>Password Reset Request</h1>
            <p><strong>{{ $body }}</strong>.</p>
            <p>You can reset your password by clicking the button below:</p>
            <p><a href="{{ $action }}" target="_blank">Reset Your Password</a></p>
            <p>If you didn't request this, you can ignore this email or contact our support team if you have questions.</p>
            <p>Thank you, <br>The Morrison</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Morrison Markets. All rights reserved.
        </div>
    </div>


    </body>
</html>
