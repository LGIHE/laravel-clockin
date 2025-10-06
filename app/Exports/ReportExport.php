<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportExport implements FromArray, WithHeadings, WithTitle
{
    protected array $data;
    protected string $type;

    public function __construct(array $data, string $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        switch ($this->type) {
            case 'individual':
                return $this->formatIndividualReport();

            case 'summary':
                return $this->formatSummaryReport();

            case 'timesheet':
                return $this->formatTimesheetReport();

            default:
                return [];
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        switch ($this->type) {
            case 'individual':
                return ['Date', 'Clock In', 'Clock Out', 'Worked Hours', 'In Message', 'Out Message'];

            case 'summary':
                return ['User', 'Email', 'Department', 'Days Present', 'Total Hours', 'Average Hours/Day', 'Late Arrivals', 'Early Departures', 'Attendance Rate'];

            case 'timesheet':
                return ['Date', 'Day', 'Clock In', 'Clock Out', 'Worked Hours', 'Status'];

            default:
                return [];
        }
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return ucfirst($this->type) . ' Report';
    }

    /**
     * Format individual report data.
     *
     * @return array
     */
    private function formatIndividualReport(): array
    {
        $rows = [];

        if (isset($this->data['attendances'])) {
            foreach ($this->data['attendances'] as $attendance) {
                $rows[] = [
                    $attendance['in_time'] ? date('Y-m-d', strtotime($attendance['in_time'])) : '',
                    $attendance['in_time'] ? date('H:i:s', strtotime($attendance['in_time'])) : '',
                    $attendance['out_time'] ? date('H:i:s', strtotime($attendance['out_time'])) : 'Not clocked out',
                    $attendance['worked_hours'] ?? '00:00:00',
                    $attendance['in_message'] ?? '',
                    $attendance['out_message'] ?? '',
                ];
            }
        }

        return $rows;
    }

    /**
     * Format summary report data.
     *
     * @return array
     */
    private function formatSummaryReport(): array
    {
        $rows = [];

        if (isset($this->data['summary'])) {
            foreach ($this->data['summary'] as $item) {
                $user = $item['user'];
                $stats = $item['statistics'];

                $rows[] = [
                    $user['name'],
                    $user['email'],
                    $user['department'] ?? 'N/A',
                    $stats['days_present'],
                    $stats['total_hours'],
                    $stats['average_hours_per_day'],
                    $stats['late_arrivals'],
                    $stats['early_departures'],
                    $stats['attendance_rate'] . '%',
                ];
            }
        }

        return $rows;
    }

    /**
     * Format timesheet report data.
     *
     * @return array
     */
    private function formatTimesheetReport(): array
    {
        $rows = [];

        if (isset($this->data['daily_records'])) {
            foreach ($this->data['daily_records'] as $record) {
                if (!empty($record['attendances'])) {
                    foreach ($record['attendances'] as $attendance) {
                        $rows[] = [
                            $record['date'],
                            $record['day_name'],
                            $attendance['in_time'] ? date('H:i:s', strtotime($attendance['in_time'])) : '',
                            $attendance['out_time'] ? date('H:i:s', strtotime($attendance['out_time'])) : 'Not clocked out',
                            $attendance['worked_hours'] ?? '00:00:00',
                            $record['status'],
                        ];
                    }
                } else {
                    $rows[] = [
                        $record['date'],
                        $record['day_name'],
                        '',
                        '',
                        '00:00:00',
                        $record['status'],
                    ];
                }
            }
        }

        return $rows;
    }
}
