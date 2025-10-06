<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignProjectsRequest;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of projects.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::query()->withCount('users');

        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Apply status filter if provided
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Apply sorting
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->input('per_page', 10);
        $projects = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ProjectResource::collection($projects),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
                'last_page' => $projects->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(CreateProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => new ProjectResource($project),
        ], 201);
    }

    /**
     * Display the specified project.
     */
    public function show(string $id): JsonResponse
    {
        $project = Project::withCount('users')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, string $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $updatedProject = $this->projectService->updateProject($project, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => new ProjectResource($updatedProject),
        ]);
    }

    /**
     * Remove the specified project.
     */
    public function destroy(string $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        try {
            $this->projectService->deleteProject($project);

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PROJECT_HAS_USERS',
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }
    }

    /**
     * Get all users assigned to a project.
     */
    public function users(string $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $users = $this->projectService->getProjectUsers($project);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ]);
    }

    /**
     * Assign users to a project.
     */
    public function assignUsers(AssignProjectsRequest $request, string $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $this->projectService->assignUsers($project, $request->input('user_ids'));

        return response()->json([
            'success' => true,
            'message' => 'Users assigned to project successfully',
        ]);
    }

    /**
     * Remove a user from a project.
     */
    public function removeUser(string $id, string $userId): JsonResponse
    {
        $project = Project::findOrFail($id);
        $this->projectService->removeUser($project, $userId);

        return response()->json([
            'success' => true,
            'message' => 'User removed from project successfully',
        ]);
    }
}
