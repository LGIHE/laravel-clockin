<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Summary Attendance Report</title>
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
        .period {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        .summary-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .overall-stats {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .overall-stats h3 {
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
        <h1>Summary Attendance Report</h1>
    </div>

    <div class="period">
        <strong>Period:</strong> {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}
    </div>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Email</th>
                <th>Department</th>
                <th>Days Present</th>
                <th>Total Hours</th>
                <th>Avg Hours/Day</th>
                <th>Late Arrivals</th>
                <th>Early Departures</th>
                <th>Attendance Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['summary'] as $item)
                <tr>
                    <td>{{ $item['user']['name'] }}</td>
                    <td>{{ $item['user']['email'] }}</td>
                    <td>{{ $item['user']['department'] ?? 'N/A' }}</td>
                    <td>{{ $item['statistics']['days_present'] }}</td>
                    <td>{{ $item['statistics']['total_hours'] }}</td>
                    <td>{{ $item['statistics']['average_hours_per_day'] }}</td>
                    <td>{{ $item['statistics']['late_arrivals'] }}</td>
                    <td>{{ $item['statistics']['early_departures'] }}</td>
                    <td>{{ $item['statistics']['attendance_rate'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="overall-stats">
        <h3>Overall Statistics</h3>
        <div class="stat-row">
            <span>Total Users:</span>
            <span>{{ $data['overall_statistics']['total_users'] }}</span>
        </div>
        <div class="stat-row">
            <span>Total Hours:</span>
            <span>{{ $data['overall_statistics']['total_hours'] }}</span>
        </div>
        <div class="stat-row">
            <span>Average Hours per User:</span>
            <span>{{ $data['overall_statistics']['average_hours_per_user'] }}</span>
        </div>
        <div class="stat-row">
            <span>Total Days Present:</span>
            <span>{{ $data['overall_statistics']['total_days_present'] }}</span>
        </div>
        <div class="stat-row">
            <span>Total Late Arrivals:</span>
            <span>{{ $data['overall_statistics']['total_late_arrivals'] }}</span>
        </div>
        <div class="stat-row">
            <span>Total Early Departures:</span>
            <span>{{ $data['overall_statistics']['total_early_departures'] }}</span>
        </div>
    </div>
</body>
</html>
