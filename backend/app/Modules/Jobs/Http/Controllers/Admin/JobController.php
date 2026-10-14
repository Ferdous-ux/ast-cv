<?php

namespace App\Modules\Jobs\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Modules\Jobs\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query()
            ->platform()
            ->with([
                'creator',
                'skills',
            ])
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'company_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'location',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $jobs = $query
            ->paginate(15)
            ->withQueryString();

        $totalJobs = Job::query()
            ->platform()
            ->count();

        $activeJobs = Job::query()
            ->platform()
            ->where('status', 'active')
            ->count();

        return view(
            'admin.jobs.index',
            compact(
                'jobs',
                'totalJobs',
                'activeJobs'
            )
        );
    }

    public function create()
    {
        $skills = Skill::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view(
            'admin.jobs.create',
            compact('skills')
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateJob($request);

        DB::transaction(function () use ($data) {
            $job = Job::query()->create([
                'created_by' => auth()->id(),

                'source_type' => 'platform',
                'source_url' => null,

                'title' => $data['title'],

                'company_name' =>
                    $data['company_name'] ?? null,

                'location' =>
                    $data['location'] ?? null,

                'employment_type' =>
                    $data['employment_type'] ?? null,

                'workplace_type' =>
                    $data['workplace_type'] ?? null,

                'description' =>
                    $data['description'],

                'requirements' =>
                    $data['requirements'] ?? null,

                'minimum_experience_years' =>
                    $data['minimum_experience_years']
                        ?? null,

                'education_level' =>
                    $data['education_level'] ?? null,

                'status' =>
                    $data['status'],

                'published_at' =>
                    $data['status'] === 'active'
                        ? now()
                        : null,
            ]);

            $job->skills()->sync(
                $this->prepareSkills($data)
            );
        });

        return redirect()
            ->route('admin.jobs.index')
            ->with(
                'success',
                __('jobs.create_success')
            );
    }

    public function show(Job $job)
    {
        $this->ensurePlatformJob($job);

        $job->load([
            'creator',
            'skills',
        ]);

        return view(
            'admin.jobs.show',
            compact('job')
        );
    }

    public function edit(Job $job)
    {
        $this->ensurePlatformJob($job);

        $job->load('skills');

        $skills = Skill::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view(
            'admin.jobs.edit',
            compact(
                'job',
                'skills'
            )
        );
    }

    public function update(
        Request $request,
        Job $job
    ) {
        $this->ensurePlatformJob($job);

        $data = $this->validateJob($request);

        DB::transaction(
            function () use (
                $job,
                $data
            ) {
                $publishedAt =
                    $job->published_at;

                if (
                    $data['status'] === 'active'
                    && !$publishedAt
                ) {
                    $publishedAt = now();
                }

                if (
                    $data['status'] !== 'active'
                ) {
                    $publishedAt = null;
                }

                $job->update([
                    'title' =>
                        $data['title'],

                    'company_name' =>
                        $data['company_name']
                            ?? null,

                    'location' =>
                        $data['location']
                            ?? null,

                    'employment_type' =>
                        $data['employment_type']
                            ?? null,

                    'workplace_type' =>
                        $data['workplace_type']
                            ?? null,

                    'description' =>
                        $data['description'],

                    'requirements' =>
                        $data['requirements']
                            ?? null,

                    'minimum_experience_years' =>
                        $data[
                            'minimum_experience_years'
                        ] ?? null,

                    'education_level' =>
                        $data['education_level']
                            ?? null,

                    'status' =>
                        $data['status'],

                    'published_at' =>
                        $publishedAt,
                ]);

                $job->skills()->sync(
                    $this->prepareSkills($data)
                );
            }
        );

        return redirect()
            ->route('admin.jobs.index')
            ->with(
                'success',
                __('jobs.update_success')
            );
    }

    public function destroy(Job $job)
    {
        $this->ensurePlatformJob($job);

        DB::transaction(
            function () use ($job) {
                $job->skills()->detach();
                $job->delete();
            }
        );

        return redirect()
            ->route('admin.jobs.index')
            ->with(
                'success',
                __('jobs.delete_success')
            );
    }

    private function validateJob(
        Request $request
    ): array {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'location' => [
                'nullable',
                'string',
                'max:150',
            ],

            'employment_type' => [
                'nullable',
                'in:full_time,part_time,contract,internship,temporary',
            ],

            'workplace_type' => [
                'nullable',
                'in:onsite,remote,hybrid',
            ],

            'description' => [
                'required',
                'string',
            ],

            'requirements' => [
                'nullable',
                'string',
            ],

            'minimum_experience_years' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
            ],

            'education_level' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                'in:draft,active,closed',
            ],

            'skill_ids' => [
                'nullable',
                'array',
            ],

            'skill_ids.*' => [
                'integer',
                'distinct',
                'exists:skills,id',
            ],

            'importance' => [
                'nullable',
                'array',
            ],

            'importance.*' => [
                'nullable',
                'in:required,preferred',
            ],

            'weight' => [
                'nullable',
                'array',
            ],

            'weight.*' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'minimum_level' => [
                'nullable',
                'array',
            ],

            'minimum_level.*' => [
                'nullable',
                'in:beginner,intermediate,advanced,expert',
            ],
        ]);
    }

    private function prepareSkills(
        array $data
    ): array {
        $syncData = [];

        foreach (
            $data['skill_ids'] ?? []
            as $skillId
        ) {
            $skillId = (int) $skillId;

            $importance =
                $data['importance'][$skillId]
                    ?? 'required';

            $weight = (int) (
                $data['weight'][$skillId]
                    ?? 100
            );

            $minimumLevel =
                $data['minimum_level'][$skillId]
                    ?? null;

            $syncData[$skillId] = [
                'importance' =>
                    $importance,

                'weight' =>
                    max(
                        1,
                        min(
                            100,
                            $weight
                        )
                    ),

                'minimum_level' =>
                    filled($minimumLevel)
                        ? $minimumLevel
                        : null,
            ];
        }

        return $syncData;
    }

    private function ensurePlatformJob(
        Job $job
    ): void {
        abort_unless(
            $job->source_type === 'platform',
            404
        );
    }
}