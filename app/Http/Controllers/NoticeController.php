<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNoticeRequest;
use App\Http\Requests\UpdateNoticeRequest;
use App\Http\Resources\NoticeResource;
use App\Models\Notice;
use Illuminate\Http\JsonResponse;

class NoticeController extends Controller
{
    /**
     * Display a listing of notices.
     */
    public function index(): JsonResponse
    {
        $notices = Notice::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => NoticeResource::collection($notices),
        ]);
    }

    /**
     * Store a newly created notice.
     */
    public function store(CreateNoticeRequest $request): JsonResponse
    {
        $notice = Notice::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'data' => new NoticeResource($notice),
            'message' => 'Notice created successfully',
        ], 201);
    }

    /**
     * Display the specified notice.
     */
    public function show(string $id): JsonResponse
    {
        $notice = Notice::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new NoticeResource($notice),
        ]);
    }

    /**
     * Update the specified notice.
     */
    public function update(UpdateNoticeRequest $request, string $id): JsonResponse
    {
        $notice = Notice::findOrFail($id);

        $notice->update([
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'data' => new NoticeResource($notice),
            'message' => 'Notice updated successfully',
        ]);
    }

    /**
     * Remove the specified notice.
     */
    public function destroy(string $id): JsonResponse
    {
        $notice = Notice::findOrFail($id);

        $notice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notice deleted successfully',
        ]);
    }
}
