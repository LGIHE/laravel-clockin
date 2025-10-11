<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Monthly Timesheet</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header-section {
            margin-bottom: 25px;
        }
        .header-row {
            display: flex;
            margin-bottom: 8px;
        }
        .header-label {
            font-weight: bold;
            width: 180px;
        }
        .header-value {
            flex: 1;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
        }
        .timesheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        .timesheet-table th,
        .timesheet-table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
        }
        .timesheet-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        .timesheet-table td.date-col {
            width: 100px;
        }
        .timesheet-table td.day-col {
            width: 80px;
        }
        .timesheet-table td.time-col {
            width: 70px;
            text-align: center;
        }
        .timesheet-table td.hours-col {
            width: 60px;
            text-align: center;
        }
        .timesheet-table td.status-col {
            width: 90px;
            text-align: center;
        }
        .summary-section {
            margin-top: 25px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .summary-row {
            padding: 5px 0;
            display: flex;
        }
        .summary-label {
            font-weight: bold;
            width: 200px;
        }
        .summary-value {
            flex: 1;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-row {
            margin-bottom: 30px;
        }
        .signature-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .signature-line {
            display: inline-block;
            width: 300px;
            border-bottom: 1px solid #333;
            margin-left: 10px;
        }
        .signature-name {
            margin-left: 160px;
            margin-top: 5px;
            font-style: italic;
        }
        .notes-section {
            margin-top: 40px;
            font-size: 9px;
            color: #666;
        }
        .notes-section h4 {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .notes-section p {
            margin: 3px 0;
            line-height: 1.4;
        }
        .status-present { color: #059669; }
        .status-absent { color: #dc2626; }
        .status-leave { color: #d97706; }
        .status-holiday { color: #2563eb; }
        .status-weekend { color: #6b7280; }
        .weekend-cell { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="header-row">
            <div class="header-label">NAME OF ORGANISATION:</div>
            <div class="header-value">LUIGI GIUSSANI FOUNDATION</div>
        </div>
        <div class="header-row">
            <div class="header-label">PROJECT NAME:</div>
            <div class="header-value">{{ $projectNames }}</div>
        </div>
        <div class="title">TIME SHEET</div>
        <div class="header-row">
            <div class="header-label">NAME OF PERSON:</div>
            <div class="header-value">{{ $user->name }}</div>
        </div>
        <div class="header-row">
            <div class="header-label">POSITION:</div>
            <div class="header-value">{{ $user->position }}</div>
        </div>
        <div class="header-row">
            <div class="header-label">PERIOD COVERED:</div>
            <div class="header-value">{{ $period }}</div>
        </div>
    </div>

    <table class="timesheet-table">
        <thead>
            <tr>
                <th class="date-col">Date</th>
                <th class="day-col">Day</th>
                <th class="time-col">Clock In</th>
                <th class="time-col">Clock Out</th>
                <th class="hours-col">Hours</th>
                <th class="status-col">Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                <tr>
                    <td class="date-col">{{ $entry['date'] }}</td>
                    <td class="day-col">{{ $entry['day'] }}</td>
                    <td class="time-col">{{ $entry['clockIn'] }}</td>
                    <td class="time-col">{{ $entry['clockOut'] }}</td>
                    <td class="hours-col">{{ $entry['hoursWorked'] }}</td>
                    <td class="status-col 
                        @if($entry['status'] === 'Present') status-present
                        @elseif($entry['status'] === 'Absent') status-absent
                        @elseif(str_contains($entry['status'], 'Leave')) status-leave
                        @elseif($entry['status'] === 'Public Holiday') status-holiday
                        @elseif($entry['status'] === 'Weekend') status-weekend
                        @endif">
                        {{ $entry['status'] }}
                    </td>
                    <td>{{ $entry['notes'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-section">
        <h3 style="margin-top: 0;">SUMMARY</h3>
        <div class="summary-row">
            <div class="summary-label">NUMBER OF DAYS WORKED:</div>
            <div class="summary-value">{{ $summary['daysWorked'] }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">NUMBER OF HOURS WORKED:</div>
            <div class="summary-value">{{ $summary['totalHours'] }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">SICK LEAVE DAYS:</div>
            <div class="summary-value">{{ $summary['sickLeaveDays'] }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">ANNUAL LEAVE DAYS:</div>
            <div class="summary-value">{{ $summary['annualLeaveDays'] }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">PUBLIC HOLIDAYS:</div>
            <div class="summary-value">{{ $summary['publicHolidays'] }}</div>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-row">
            <span class="signature-label">SIGNED:</span>
            <span class="signature-line"></span>
            <div class="signature-name">({{ $user->name }})</div>
            <div style="margin-top: 15px;">
                <span class="signature-label">DATE:</span>
                <span>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="signature-row" style="margin-top: 30px;">
            <span class="signature-label">APPROVED BY:</span>
            <span class="signature-line"></span>
            <div class="signature-name">({{ strtoupper($user->supervisor ? $user->supervisor->name : 'N/A') }})</div>
            <div style="margin-top: 15px; margin-left: 0;">
                <span class="signature-label">POSITION:</span>
                <span>{{ $user->supervisor ? $user->supervisor->position : 'N/A' }}</span>
            </div>
            <div style="margin-top: 10px;">
                <span class="signature-label">DATE:</span>
                <span class="signature-line" style="width: 200px;"></span>
            </div>
        </div>
    </div>

    <div class="notes-section">
        <h4>Explanatory notes</h4>
        <p>- This timesheet template is adapted for use in cases where a person is working for several projects or tasks in the same period</p>
        <p>- To avoid errors, weekends and public holidays are clearly marked</p>
        <p>- This timesheet includes daily attendance records, approved leaves, and public holidays</p>
    </div>
</body>
</html>
