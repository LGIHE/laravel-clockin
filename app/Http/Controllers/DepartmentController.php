<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Department::query()->withCount('users');

        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->input('per_page', 10);
        $departments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => DepartmentResource::collection($departments),
            'meta' => [
                'current_page' => $departments->currentPage(),
                'per_page' => $departments->perPage(),
                'total' => $departments->total(),
                'last_page' => $departments->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(CreateDepartmentRequest $request): JsonResponse
    {
        $department = Department::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully',
            'data' => new DepartmentResource($department),
        ], 201);
    }

    /**
     * Display the specified department.
     */
    public function show(string $id): JsonResponse
    {
        $department = Department::withCount('users')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new DepartmentResource($department),
        ]);
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, string $id): JsonResponse
    {
        $department = Department::findOrFail($id);

        $department->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully',
            'data' => new DepartmentResource($department),
        ]);
    }

    /**
     * Remove the specified department.
     */
    public function destroy(string $id): JsonResponse
    {
        $department = Department::withCount('users')->findOrFail($id);

        // Check if department has active users
        if ($department->users_count > 0) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DEPARTMENT_HAS_USERS',
                    'message' => 'Cannot delete department with active users',
                ],
            ], 400);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully',
        ]);
    }
}
