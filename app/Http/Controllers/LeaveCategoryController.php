<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeaveCategoryRequest;
use App\Http\Requests\UpdateLeaveCategoryRequest;
use App\Http\Resources\LeaveCategoryResource;
use App\Models\LeaveCategory;
use Illuminate\Http\JsonResponse;

class LeaveCategoryController extends Controller
{
    /**
     * Display a listing of leave categories.
     */
    public function index(): JsonResponse
    {
        $categories = LeaveCategory::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => LeaveCategoryResource::collection($categories),
        ]);
    }

    /**
     * Store a newly created leave category.
     */
    public function store(CreateLeaveCategoryRequest $request): JsonResponse
    {
        $category = LeaveCategory::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => $request->name,
            'max_in_year' => $request->max_in_year,
        ]);

        return response()->json([
            'success' => true,
            'data' => new LeaveCategoryResource($category),
            'message' => 'Leave category created successfully',
        ], 201);
    }

    /**
     * Display the specified leave category.
     */
    public function show(string $id): JsonResponse
    {
        $category = LeaveCategory::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new LeaveCategoryResource($category),
        ]);
    }

    /**
     * Update the specified leave category.
     */
    public function update(UpdateLeaveCategoryRequest $request, string $id): JsonResponse
    {
        $category = LeaveCategory::findOrFail($id);

        $category->update([
            'name' => $request->name,
            'max_in_year' => $request->max_in_year,
        ]);

        return response()->json([
            'success' => true,
            'data' => new LeaveCategoryResource($category),
            'message' => 'Leave category updated successfully',
        ]);
    }

    /**
     * Remove the specified leave category.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = LeaveCategory::findOrFail($id);

        // Check if there are active leaves using this category
        $activeLeaves = $category->leaves()->whereNull('deleted_at')->count();

        if ($activeLeaves > 0) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CATEGORY_IN_USE',
                    'message' => 'Cannot delete leave category with active leaves',
                ],
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Leave category deleted successfully',
        ]);
    }
}
