<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Approved</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            color: #10b981;
            margin-top: 0;
        }
        .info-box {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 10px 0;
        }
        .info-box strong {
            color: #059669;
        }
        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background-color: #10b981;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
        }
        .cta-button:hover {
            background-color: #059669;
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
            <h1>✅ Leave Request Approved</h1>
        </div>
        
        <div class="email-body">
            <h2>Hello {{ $applicant->name }},</h2>
            
            <p>Great news! Your leave request has been approved.</p>
            
            <div class="info-box">
                <p><strong>📅 Leave Date:</strong> {{ $leave->date->format('l, F j, Y') }}</p>
                <p><strong>📂 Leave Type:</strong> {{ $leave->category->name }}</p>
                <p><strong>✅ Approved By:</strong> {{ $approver->name }}</p>
                @if($leave->description)
                <p><strong>📝 Your Reason:</strong> {{ $leave->description }}</p>
                @endif
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('leaves.index') }}" class="cta-button">View Leave Details</a>
            </div>
            
            <p>Enjoy your time off!</p>
        </div>
        
        <div class="email-footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ClockIn System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
