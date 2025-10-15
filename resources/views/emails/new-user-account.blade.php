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
        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .credentials-box p {
            margin: 10px 0;
        }
        .credentials-box strong {
            color: #667eea;
            display: inline-block;
            min-width: 120px;
        }
        .credentials-box .password {
            font-family: 'Courier New', monospace;
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 16px;
            letter-spacing: 1px;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 5px 0;
            color: #856404;
        }
        .cta-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #667eea;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
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
        .steps {
            list-style: none;
            padding: 0;
            counter-reset: step-counter;
        }
        .steps li {
            counter-increment: step-counter;
            margin: 15px 0;
            padding-left: 40px;
            position: relative;
        }
        .steps li:before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background-color: #667eea;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            text-align: center;
            line-height: 25px;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Welcome to ClockIn!</h1>
        </div>
        
        <div class="email-body">
            <h2>Hello {{ $name }},</h2>
            
            <p>Your account has been successfully created in our ClockIn system. We're excited to have you on board!</p>
            
            <div class="credentials-box">
                <h3 style="margin-top: 0; color: #667eea;">Your Login Credentials</h3>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Temporary Password:</strong> <span class="password">{{ $password }}</span></p>
            </div>
            
            <div class="warning-box">
                <p><strong>⚠️ Important Security Notice:</strong></p>
                <p>For security reasons, you will be required to change your password upon your first login. Please keep this temporary password secure and do not share it with anyone.</p>
            </div>
            
            <h3>Getting Started</h3>
            <ol class="steps">
                <li>Click the button below to access the login page</li>
                <li>Enter your email and temporary password</li>
                <li>Create a new secure password when prompted</li>
                <li>You'll be automatically signed in to your account</li>
            </ol>
            
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="cta-button">Login to ClockIn</a>
            </div>
            
            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                <strong>Need help?</strong> If you have any questions or encounter any issues, please contact your administrator.
            </p>
        </div>
        
        <div class="email-footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ClockIn System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
