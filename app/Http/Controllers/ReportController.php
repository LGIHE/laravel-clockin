<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate individual attendance report for a specific user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function individual(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|string|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $report = $this->reportService->generateIndividualReport([
                'user_id' => $request->input('user_id'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'REPORT_GENERATION_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Generate summary report for multiple users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'nullable|string|exists:users,id',
            'department_id' => 'nullable|string|exists:departments,id',
            'project_id' => 'nullable|string|exists:projects,id',
        ]);

        try {
            $report = $this->reportService->generateSummaryReport([
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'user_id' => $request->input('user_id'),
                'department_id' => $request->input('department_id'),
                'project_id' => $request->input('project_id'),
            ]);

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'REPORT_GENERATION_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Generate timesheet for a specific user and month.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function timesheet(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|string|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        try {
            $report = $this->reportService->generateTimesheet([
                'user_id' => $request->input('user_id'),
                'month' => $request->input('month'),
                'year' => $request->input('year'),
            ]);

            return response()->json([
                'success' => true,
                'data' => $report,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'REPORT_GENERATION_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Export report in specified format (PDF, Excel, CSV).
     *
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:individual,summary,timesheet',
            'format' => 'required|in:pdf,excel,csv',
            'user_id' => 'required_if:type,individual,timesheet|string|exists:users,id',
            'start_date' => 'required_if:type,individual,summary|date',
            'end_date' => 'required_if:type,individual,summary|date|after_or_equal:start_date',
            'month' => 'required_if:type,timesheet|integer|min:1|max:12',
            'year' => 'required_if:type,timesheet|integer|min:2000|max:2100',
            'department_id' => 'nullable|string|exists:departments,id',
            'project_id' => 'nullable|string|exists:projects,id',
        ]);

        try {
            $type = $request->input('type');
            $format = $request->input('format');

            // Generate report data based on type
            $reportData = $this->getReportData($type, $request);

            // Export based on format
            return $this->exportReport($reportData, $type, $format);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'EXPORT_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Get report data based on type.
     *
     * @param string $type
     * @param Request $request
     * @return array
     */
    private function getReportData(string $type, Request $request): array
    {
        switch ($type) {
            case 'individual':
                return $this->reportService->generateIndividualReport([
                    'user_id' => $request->input('user_id'),
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                ]);

            case 'summary':
                return $this->reportService->generateSummaryReport([
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                    'user_id' => $request->input('user_id'),
                    'department_id' => $request->input('department_id'),
                    'project_id' => $request->input('project_id'),
                ]);

            case 'timesheet':
                return $this->reportService->generateTimesheet([
                    'user_id' => $request->input('user_id'),
                    'month' => $request->input('month'),
                    'year' => $request->input('year'),
                ]);

            default:
                throw new \Exception('Invalid report type');
        }
    }

    /**
     * Export report in specified format.
     *
     * @param array $data
     * @param string $type
     * @param string $format
     * @return mixed
     */
    private function exportReport(array $data, string $type, string $format)
    {
        $filename = $type . '_report_' . date('Y-m-d_His');

        switch ($format) {
            case 'pdf':
                return $this->exportPdf($data, $type, $filename);

            case 'excel':
                return $this->exportExcel($data, $type, $filename);

            case 'csv':
                return $this->exportCsv($data, $type, $filename);

            default:
                throw new \Exception('Invalid export format');
        }
    }

    /**
     * Export report as PDF.
     *
     * @param array $data
     * @param string $type
     * @param string $filename
     * @return mixed
     */
    private function exportPdf(array $data, string $type, string $filename)
    {
        $pdf = Pdf::loadView('reports.' . $type . '_pdf', ['data' => $data]);
        return $pdf->download($filename . '.pdf');
    }

    /**
     * Export report as Excel.
     *
     * @param array $data
     * @param string $type
     * @param string $filename
     * @return mixed
     */
    private function exportExcel(array $data, string $type, string $filename)
    {
        return Excel::download(new ReportExport($data, $type), $filename . '.xlsx');
    }

    /**
     * Export report as CSV.
     *
     * @param array $data
     * @param string $type
     * @param string $filename
     * @return mixed
     */
    private function exportCsv(array $data, string $type, string $filename)
    {
        return Excel::download(new ReportExport($data, $type), $filename . '.csv');
    }
}
