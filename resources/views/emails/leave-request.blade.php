<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Leave Request</title>
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📋 New Leave Request</h1>
        </div>
        
        <div class="email-body">
            <h2>Hello {{ $supervisor->name }},</h2>
            
            <p>{{ $applicant->name }} has submitted a new leave request that requires your review.</p>
            
            <div class="info-box">
                <p><strong>👤 Employee:</strong> {{ $applicant->name }}</p>
                <p><strong>📧 Email:</strong> {{ $applicant->email }}</p>
                <p><strong>📅 Leave Date:</strong> {{ $leave->date->format('l, F j, Y') }}</p>
                <p><strong>📂 Leave Type:</strong> {{ $leave->category->name }}</p>
                @if($leave->description)
                <p><strong>📝 Reason:</strong> {{ $leave->description }}</p>
                @endif
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('leaves.index') }}" class="cta-button">Review Leave Request</a>
            </div>
            
            <p>Please review and take action on this leave request at your earliest convenience.</p>
        </div>
        
        <div class="email-footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ClockIn System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
