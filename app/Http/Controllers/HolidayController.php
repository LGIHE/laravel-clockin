<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHolidayRequest;
use App\Http\Requests\UpdateHolidayRequest;
use App\Http\Resources\HolidayResource;
use App\Models\Holiday;
use Illuminate\Http\JsonResponse;

class HolidayController extends Controller
{
    /**
     * Display a listing of holidays.
     */
    public function index(): JsonResponse
    {
        $holidays = Holiday::orderBy('date')->get();

        return response()->json([
            'success' => true,
            'data' => HolidayResource::collection($holidays),
        ]);
    }

    /**
     * Store a newly created holiday.
     */
    public function store(CreateHolidayRequest $request): JsonResponse
    {
        $holiday = Holiday::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'date' => $request->date,
        ]);

        return response()->json([
            'success' => true,
            'data' => new HolidayResource($holiday),
            'message' => 'Holiday created successfully',
        ], 201);
    }

    /**
     * Display the specified holiday.
     */
    public function show(string $id): JsonResponse
    {
        $holiday = Holiday::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new HolidayResource($holiday),
        ]);
    }

    /**
     * Update the specified holiday.
     */
    public function update(UpdateHolidayRequest $request, string $id): JsonResponse
    {
        $holiday = Holiday::findOrFail($id);

        $holiday->update([
            'date' => $request->date,
        ]);

        return response()->json([
            'success' => true,
            'data' => new HolidayResource($holiday),
            'message' => 'Holiday updated successfully',
        ]);
    }

    /**
     * Remove the specified holiday.
     */
    public function destroy(string $id): JsonResponse
    {
        $holiday = Holiday::findOrFail($id);

        $holiday->delete();

        return response()->json([
            'success' => true,
            'message' => 'Holiday deleted successfully',
        ]);
    }
}
