<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeSkillRequest;
use App\Http\Requests\UpdateResumeSkillRequest;
use App\Models\Resume;
use App\Models\ResumeSkill;
use Illuminate\Http\JsonResponse;

class ResumeSkillController extends Controller
{
    public function index(Resume $resume): JsonResponse
    {
        $this->ensureResumeOwnership($resume);

        $skills = ResumeSkill::query()
            ->with('skill')
            ->whereHas('resumeVersion', function ($query) use ($resume) {
                $query->where('resume_id', $resume->id);
            })
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $skills,
        ]);
    }

    public function store(
        StoreResumeSkillRequest $request,
        Resume $resume
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $version = $resume->currentVersion;

        $skill = ResumeSkill::create([
            'resume_version_id' => $version->id,
            'skill_id' => $request->validated()['skill_id'],
            'level' => $request->validated()['level'] ?? null,
            'sort_order' => $request->validated()['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Skill added successfully.',
            'data' => $skill->load('skill'),
        ], 201);
    }

    public function show(
        Resume $resume,
        ResumeSkill $skill
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureSkillBelongsToResume(
            $resume,
            $skill
        );

        return response()->json([
            'data' => $skill->load('skill'),
        ]);
    }

    public function update(
        UpdateResumeSkillRequest $request,
        Resume $resume,
        ResumeSkill $skill
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureSkillBelongsToResume(
            $resume,
            $skill
        );

        $skill->update($request->validated());

        return response()->json([
            'message' => 'Skill updated successfully.',
            'data' => $skill->fresh()->load('skill'),
        ]);
    }

    public function destroy(
        Resume $resume,
        ResumeSkill $skill
    ): JsonResponse {
        $this->ensureResumeOwnership($resume);

        $this->ensureSkillBelongsToResume(
            $resume,
            $skill
        );

        $skill->delete();

        return response()->json([
            'message' => 'Skill removed successfully.',
        ]);
    }

    private function ensureResumeOwnership(
        Resume $resume
    ): void {
        abort_unless(
            $resume->user_id === auth()->id(),
            403
        );
    }

    private function ensureSkillBelongsToResume(
        Resume $resume,
        ResumeSkill $skill
    ): void {
        abort_unless(
            $skill->resumeVersion->resume_id === $resume->id,
            404
        );
    }
}