<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyLeaveRequest;
use App\Http\Requests\ReviewLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use App\Http\Resources\LeaveResource;
use App\Models\Leave;
use App\Models\LeaveStatus;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    protected LeaveService $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Display a listing of leaves.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Leave::with(['user', 'category', 'status']);

        // Filter by user role
        if ($user->role === 'USER') {
            // Regular users see only their leaves
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'SUPERVISOR') {
            // Supervisors see their team's leaves (users they supervise)
            $teamUserIds = \App\Models\User::where('supervisor_id', $user->id)->pluck('id');
            $query->whereIn('user_id', $teamUserIds->push($user->id));
        }
        // Admins see all leaves (no filter)

        // Apply filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $status = LeaveStatus::where('name', $request->status)->first();
            if ($status) {
                $query->where('leave_status_id', $status->id);
            }
        }

        if ($request->has('category_id')) {
            $query->where('leave_category_id', $request->category_id);
        }

        if ($request->has('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $leaves = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => LeaveResource::collection($leaves),
            'meta' => [
                'current_page' => $leaves->currentPage(),
                'per_page' => $leaves->perPage(),
                'total' => $leaves->total(),
                'last_page' => $leaves->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created leave.
     */
    public function store(ApplyLeaveRequest $request): JsonResponse
    {
        try {
            $leave = $this->leaveService->applyLeave(
                $request->user()->id,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Leave application submitted successfully',
                'data' => new LeaveResource($leave),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_APPLICATION_FAILED',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Display the specified leave.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $leave = Leave::with(['user', 'category', 'status'])->find($id);

        if (!$leave) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_NOT_FOUND',
                    'message' => 'Leave not found',
                ],
            ], 404);
        }

        // Check authorization
        $user = $request->user();
        if ($user->role === 'USER' && $leave->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You are not authorized to view this leave',
                ],
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new LeaveResource($leave),
        ]);
    }

    /**
     * Update the specified leave.
     */
    public function update(UpdateLeaveRequest $request, string $id): JsonResponse
    {
        $leave = Leave::find($id);

        if (!$leave) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_NOT_FOUND',
                    'message' => 'Leave not found',
                ],
            ], 404);
        }

        // Check authorization - only owner can update
        if ($leave->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You are not authorized to update this leave',
                ],
            ], 403);
        }

        // Check if leave is pending
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        if ($leave->leave_status_id !== $pendingStatus->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_NOT_PENDING',
                    'message' => 'Only pending leaves can be updated',
                ],
            ], 400);
        }

        $leave->update($request->validated());
        $leave->load(['user', 'category', 'status']);

        return response()->json([
            'success' => true,
            'message' => 'Leave updated successfully',
            'data' => new LeaveResource($leave),
        ]);
    }

    /**
     * Remove the specified leave.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $leave = Leave::find($id);

        if (!$leave) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_NOT_FOUND',
                    'message' => 'Leave not found',
                ],
            ], 404);
        }

        // Check authorization - only owner can delete
        if ($leave->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You are not authorized to delete this leave',
                ],
            ], 403);
        }

        // Check if leave is pending
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        if ($leave->leave_status_id !== $pendingStatus->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_NOT_PENDING',
                    'message' => 'Only pending leaves can be deleted',
                ],
            ], 400);
        }

        $leave->delete();

        return response()->json([
            'success' => true,
            'message' => 'Leave deleted successfully',
        ]);
    }

    /**
     * Approve a leave request.
     */
    public function approve(ReviewLeaveRequest $request, string $id): JsonResponse
    {
        try {
            $leave = $this->leaveService->approveLeave(
                $id,
                $request->user()->id,
                $request->input('comments')
            );

            return response()->json([
                'success' => true,
                'message' => 'Leave approved successfully',
                'data' => new LeaveResource($leave),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_APPROVAL_FAILED',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Reject a leave request.
     */
    public function reject(ReviewLeaveRequest $request, string $id): JsonResponse
    {
        try {
            $leave = $this->leaveService->rejectLeave(
                $id,
                $request->user()->id,
                $request->input('comments')
            );

            return response()->json([
                'success' => true,
                'message' => 'Leave rejected successfully',
                'data' => new LeaveResource($leave),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_REJECTION_FAILED',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Get leave balance for a user.
     */
    public function balance(Request $request): JsonResponse
    {
        $userId = $request->get('user_id', $request->user()->id);
        $year = $request->get('year', now()->year);

        // Check authorization - users can only see their own balance unless admin/supervisor
        if ($userId !== $request->user()->id && !in_array($request->user()->role, ['ADMIN', 'SUPERVISOR'])) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'You are not authorized to view this balance',
                ],
            ], 403);
        }

        try {
            $categories = \App\Models\LeaveCategory::all();
            $balances = [];

            foreach ($categories as $category) {
                $balances[] = $this->leaveService->getLeaveBalance($userId, $category->id, $year);
            }

            return response()->json([
                'success' => true,
                'data' => $balances,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'BALANCE_FETCH_FAILED',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }
}

