<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .stats {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <p>{{ $user->name }}</p>
        <p>{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">Employee Name:</span>
            <span>{{ $user->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">Email:</span>
            <span>{{ $user->email }}</span>
        </div>
        @if($user->designation)
        <div class="info-row">
            <span class="label">Designation:</span>
            <span>{{ $user->designation->name }}</span>
        </div>
        @endif
        @if($user->department)
        <div class="info-row">
            <span class="label">Department:</span>
            <span>{{ $user->department->name }}</span>
        </div>
        @endif
    </div>

    <div class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-label">Total Hours</div>
                <div class="stat-value">{{ $statistics['totalHours'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Days Worked</div>
                <div class="stat-value">{{ $statistics['daysWorked'] ?? 0 }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Attendance %</div>
                <div class="stat-value">{{ $statistics['attendancePercentage'] ?? 0 }}%</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">Date</th>
                <th style="width: 30%;">In Time</th>
                <th style="width: 30%;">Out Time</th>
                <th style="width: 10%;">Worked</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record['id'] }}</td>
                    <td>{{ $record['date'] }}</td>
                    <td>{{ $record['inTime'] }}</td>
                    <td>{{ $record['outTime'] ?? '-' }}</td>
                    <td>{{ $record['worked'] }}</td>
                    <td>{{ $record['status'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #999;">
                        No attendance records found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ \Carbon\Carbon::now()->format('M d, Y h:i A') }}</p>
        <p>© {{ date('Y') }} LUIGI GIUSSANI FOUNDATION - Attendance Management System</p>
    </div>
</body>
</html>
