<?php

namespace App\Modules\Resume\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminResumeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->get('search')
        );

        $status = trim(
            (string) $request->get('status')
        );

        $resumes = Resume::query()
            ->with([
                'user',
                'language',
                'currentVersion',
            ])
            ->withCount('versions')
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'title',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'user',
                                    function ($query) use ($search) {
                                        $query
                                            ->where(
                                                'name',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $status !== '',
                fn ($query) => $query->where(
                    'status',
                    $status
                )
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalResumes = Resume::count();

        $activeResumes = Resume::query()
            ->where(
                'status',
                'active'
            )
            ->count();

        $draftResumes = Resume::query()
            ->where(
                'status',
                'draft'
            )
            ->count();

        $totalVersions = Resume::query()
            ->withCount('versions')
            ->get()
            ->sum('versions_count');

        return view(
            'admin.resumes.index',
            compact(
                'resumes',
                'search',
                'status',
                'totalResumes',
                'activeResumes',
                'draftResumes',
                'totalVersions'
            )
        );
    }

    public function show(Resume $resume): View
    {
        $resume->load([
            'user',
            'language',

            'currentVersion.skills',

            'currentVersion.experiences',

            'currentVersion.educations',

            'currentVersion.languages',

            'versions' => fn ($query) => $query
                ->orderByDesc('version_number'),
        ]);

        return view(
            'admin.resumes.show',
            compact('resume')
        );
    }
}