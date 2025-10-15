<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ClockIn</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .email-body {
            padding: 30px;
        }
        .email-body h2 {
            color: #667eea;
            margin-top: 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 10px 0;
        }
        .info-box strong {
            color: #667eea;
        }
        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background-color: #667eea;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
        }
        .cta-button:hover {
            background-color: #5568d3;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .link-info {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .link-info p {
            margin: 5px 0;
            color: #856404;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎉 Welcome to ClockIn!</h1>
        </div>
        
        <div class="email-body">
            <h2>Hello {{ $name }},</h2>
            
            <p>Your account has been successfully created in our ClockIn system. We're excited to have you on board!</p>
            
            <div class="info-box">
                <p><strong>📧 Your Account Email:</strong> {{ $email }}</p>
                <p>Click the button below to complete your account setup by creating your password.</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $setupUrl }}" class="cta-button">Complete Account Setup</a>
            </div>
            
            <div class="link-info">
                <p><strong>🔒 Security Notice:</strong></p>
                <p>This link is valid for 24 hours and can only be used once. After creating your password, you'll be automatically logged in to your account.</p>
            </div>
            
            <p>If you did not expect this email or have any questions, please contact your administrator.</p>
        </div>
        
        <div class="email-footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ClockIn System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
