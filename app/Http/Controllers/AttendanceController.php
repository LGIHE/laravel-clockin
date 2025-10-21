<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClockInRequest;
use App\Http\Requests\ClockOutRequest;
use App\Http\Requests\ForcePunchRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Clock in the authenticated user.
     *
     * @param ClockInRequest $request
     * @return JsonResponse
     */
    public function clockIn(ClockInRequest $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->clockIn(
                auth()->id(),
                $request->input('message'),
                $request->input('project_ids', $request->input('project_id')), // Support both project_ids array and legacy project_id
                $request->input('task_ids', $request->input('task_id')) // Support both task_ids array and legacy task_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Clocked in successfully',
                'data' => new AttendanceResource($attendance),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CLOCK_IN_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Clock out the authenticated user.
     *
     * @param ClockOutRequest $request
     * @return JsonResponse
     */
    public function clockOut(ClockOutRequest $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->clockOut(
                auth()->id(),
                $request->input('message'),
                $request->input('task_statuses', $request->input('task_status')) // Support both task_statuses array and legacy task_status
            );

            return response()->json([
                'success' => true,
                'message' => 'Clocked out successfully',
                'data' => new AttendanceResource($attendance),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CLOCK_OUT_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Get the current attendance status for the authenticated user.
     *
     * @return JsonResponse
     */
    public function status(): JsonResponse
    {
        $status = $this->attendanceService->getCurrentStatus(auth()->id());

        return response()->json([
            'success' => true,
            'data' => [
                'clocked_in' => $status['clocked_in'],
                'in_time' => $status['in_time'],
                'duration' => $status['duration'],
                'attendance' => $status['attendance'] ? new AttendanceResource($status['attendance']) : null,
            ],
        ], 200);
    }

    /**
     * Get attendance records with filters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'user_id' => $request->input('user_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'sort_by' => $request->input('sort_by', 'in_time'),
            'sort_order' => $request->input('sort_order', 'desc'),
            'per_page' => $request->input('per_page', 15),
        ];

        $attendances = $this->attendanceService->getAttendanceRecords($filters);

        return response()->json([
            'success' => true,
            'data' => AttendanceResource::collection($attendances->items()),
            'meta' => [
                'current_page' => $attendances->currentPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
                'last_page' => $attendances->lastPage(),
            ],
        ], 200);
    }

    /**
     * Get a specific attendance record.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $attendance = Attendance::with(['user.userLevel', 'user.department', 'user.designation'])
            ->find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Attendance record not found',
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AttendanceResource($attendance),
        ], 200);
    }

    /**
     * Update an attendance record (admin only).
     *
     * @param UpdateAttendanceRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateAttendanceRequest $request, string $id): JsonResponse
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Attendance record not found',
                ],
            ], 404);
        }

        $data = $request->only(['in_time', 'in_message', 'out_time', 'out_message']);

        // Recalculate worked hours if both times are provided
        if (!empty($data['in_time']) && !empty($data['out_time'])) {
            $data['worked'] = $this->attendanceService->calculateWorkedHours(
                \Carbon\Carbon::parse($data['in_time']),
                \Carbon\Carbon::parse($data['out_time'])
            );
        }

        $attendance->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Attendance record updated successfully',
            'data' => new AttendanceResource($attendance->load('user')),
        ], 200);
    }

    /**
     * Delete an attendance record (admin only).
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Attendance record not found',
                ],
            ], 404);
        }

        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance record deleted successfully',
        ], 200);
    }

    /**
     * Force punch for a user (admin only).
     *
     * @param ForcePunchRequest $request
     * @return JsonResponse
     */
    public function forcePunch(ForcePunchRequest $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->forcePunch(
                $request->input('user_id'),
                $request->input('type'),
                $request->input('time'),
                $request->input('message')
            );

            return response()->json([
                'success' => true,
                'message' => 'Force punch completed successfully',
                'data' => new AttendanceResource($attendance),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FORCE_PUNCH_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }
}
