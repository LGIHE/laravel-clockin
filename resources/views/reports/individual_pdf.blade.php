<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Individual Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section table {
            width: 100%;
        }
        .info-section td {
            padding: 5px;
        }
        .info-section td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .attendance-table th,
        .attendance-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .attendance-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .statistics {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .statistics h3 {
            margin-top: 0;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Individual Attendance Report</h1>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td>Employee Name:</td>
                <td>{{ $data['user']['name'] }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $data['user']['email'] }}</td>
            </tr>
            <tr>
                <td>Department:</td>
                <td>{{ $data['user']['department'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Designation:</td>
                <td>{{ $data['user']['designation'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Period:</td>
                <td>{{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}</td>
            </tr>
        </table>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Worked Hours</th>
                <th>In Message</th>
                <th>Out Message</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['attendances'] as $attendance)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($attendance['in_time'])->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance['in_time'])->format('H:i:s') }}</td>
                    <td>{{ $attendance['out_time'] ? \Carbon\Carbon::parse($attendance['out_time'])->format('H:i:s') : 'Not clocked out' }}</td>
                    <td>{{ $attendance['worked_hours'] ?? '00:00:00' }}</td>
                    <td>{{ $attendance['in_message'] ?? '' }}</td>
                    <td>{{ $attendance['out_message'] ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No attendance records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="statistics">
        <h3>Statistics</h3>
        <div class="stat-row">
            <span>Total Days:</span>
            <span>{{ $data['statistics']['total_days'] }}</span>
        </div>
        <div class="stat-row">
            <span>Days Present:</span>
            <span>{{ $data['statistics']['days_present'] }}</span>
        </div>
        <div class="stat-row">
            <span>Days Absent:</span>
            <span>{{ $data['statistics']['days_absent'] }}</span>
        </div>
        <div class="stat-row">
            <span>Total Hours:</span>
            <span>{{ $data['statistics']['total_hours_formatted'] }}</span>
        </div>
        <div class="stat-row">
            <span>Average Hours/Day:</span>
            <span>{{ $data['statistics']['average_hours_per_day_formatted'] }}</span>
        </div>
        <div class="stat-row">
            <span>Late Arrivals:</span>
            <span>{{ $data['statistics']['late_arrivals'] }}</span>
        </div>
        <div class="stat-row">
            <span>Early Departures:</span>
            <span>{{ $data['statistics']['early_departures'] }}</span>
        </div>
        <div class="stat-row">
            <span>Attendance Rate:</span>
            <span>{{ $data['statistics']['attendance_rate'] }}%</span>
        </div>
    </div>
</body>
</html>
