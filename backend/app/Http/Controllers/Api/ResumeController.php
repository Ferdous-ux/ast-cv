<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ResumeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $resumes = $request->user()
            ->resumes()
            ->with('currentVersion')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $resumes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Resume::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
        ]);

        $resume = $request->user()->resumes()->create([
            'title' => $validated['title'],
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resume created successfully.',
            'data' => $resume,
        ], 201);
    }

    public function show(Resume $resume): JsonResponse
    {
        Gate::authorize('view', $resume);

        $resume->load([
            'currentVersion',
            'versions',
        ]);

        return response()->json([
            'success' => true,
            'data' => $resume,
        ]);
    }

    public function update(Request $request, Resume $resume): JsonResponse
    {
        Gate::authorize('update', $resume);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:150'],
        ]);

        $resume->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Resume updated successfully.',
            'data' => $resume->fresh(),
        ]);
    }

    public function destroy(Resume $resume): JsonResponse
    {
        Gate::authorize('delete', $resume);

        $resume->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resume deleted successfully.',
        ]);
    }
}

