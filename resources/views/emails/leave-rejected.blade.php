<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Rejected</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            color: #ef4444;
            margin-top: 0;
        }
        .info-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 10px 0;
        }
        .info-box strong {
            color: #dc2626;
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
            <h1>❌ Leave Request Rejected</h1>
        </div>
        
        <div class="email-body">
            <h2>Hello {{ $applicant->name }},</h2>
            
            <p>We regret to inform you that your leave request has been rejected.</p>
            
            <div class="info-box">
                <p><strong>📅 Leave Date:</strong> {{ $leave->date->format('l, F j, Y') }}</p>
                <p><strong>📂 Leave Type:</strong> {{ $leave->category->name }}</p>
                <p><strong>❌ Rejected By:</strong> {{ $rejector->name }}</p>
                @if($leave->description)
                <p><strong>📝 Your Reason:</strong> {{ $leave->description }}</p>
                @endif
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('leaves.index') }}" class="cta-button">View Leave Details</a>
            </div>
            
            <p>If you have any questions or concerns, please contact your supervisor or HR department.</p>
        </div>
        
        <div class="email-footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ClockIn System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
