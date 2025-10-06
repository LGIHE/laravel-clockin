<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get user dashboard data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->getUserDashboard($request->user()->id);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DASHBOARD_ERROR',
                    'message' => 'Failed to retrieve user dashboard data',
                    'details' => config('app.debug') ? $e->getMessage() : null,
                ],
            ], 500);
        }
    }

    /**
     * Get supervisor dashboard data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function supervisor(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->getSupervisorDashboard($request->user()->id);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DASHBOARD_ERROR',
                    'message' => 'Failed to retrieve supervisor dashboard data',
                    'details' => config('app.debug') ? $e->getMessage() : null,
                ],
            ], 500);
        }
    }

    /**
     * Get admin dashboard data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function admin(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->getAdminDashboard();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DASHBOARD_ERROR',
                    'message' => 'Failed to retrieve admin dashboard data',
                    'details' => config('app.debug') ? $e->getMessage() : null,
                ],
            ], 500);
        }
    }
}
