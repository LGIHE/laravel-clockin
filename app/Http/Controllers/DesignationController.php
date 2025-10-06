<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Http\Resources\DesignationResource;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Display a listing of designations.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Designation::query()->withCount('users');

        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->input('per_page', 10);
        $designations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => DesignationResource::collection($designations),
            'meta' => [
                'current_page' => $designations->currentPage(),
                'per_page' => $designations->perPage(),
                'total' => $designations->total(),
                'last_page' => $designations->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created designation.
     */
    public function store(CreateDesignationRequest $request): JsonResponse
    {
        $designation = Designation::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Designation created successfully',
            'data' => new DesignationResource($designation),
        ], 201);
    }

    /**
     * Display the specified designation.
     */
    public function show(string $id): JsonResponse
    {
        $designation = Designation::withCount('users')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new DesignationResource($designation),
        ]);
    }

    /**
     * Update the specified designation.
     */
    public function update(UpdateDesignationRequest $request, string $id): JsonResponse
    {
        $designation = Designation::findOrFail($id);

        $designation->update([
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Designation updated successfully',
            'data' => new DesignationResource($designation),
        ]);
    }

    /**
     * Remove the specified designation.
     */
    public function destroy(string $id): JsonResponse
    {
        $designation = Designation::withCount('users')->findOrFail($id);

        // Check if designation has active users
        if ($designation->users_count > 0) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DESIGNATION_HAS_USERS',
                    'message' => 'Cannot delete designation with active users',
                ],
            ], 400);
        }

        $designation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Designation deleted successfully',
        ]);
    }
}
