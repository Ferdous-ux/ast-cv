<?php

namespace App\Modules\Resume\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResumeTemplate;
use App\Models\ResumeVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $status = $request->get('status');

        $templates = ResumeTemplate::query()
            ->withCount('resumeVersions')
            ->when(
                $search !== '',
                fn ($query) => $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
            )
            ->when(
                $status === 'active',
                fn ($query) => $query->where('is_active', true)
            )
            ->when(
                $status === 'inactive',
                fn ($query) => $query->where('is_active', false)
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $totalTemplates = ResumeTemplate::count();
        $activeTemplates = ResumeTemplate::where('is_active', true)->count();
        $inactiveTemplates = ResumeTemplate::where('is_active', false)->count();

        return view(
            'admin.templates.index',
            compact(
                'templates',
                'totalTemplates',
                'activeTemplates',
                'inactiveTemplates',
                'search',
                'status'
            )
        );
    }

    public function create(): View
    {
        return view('admin.templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['is_active'] = $request->boolean('is_active');

        ResumeTemplate::create($data);

        return redirect()
            ->route('admin.templates.index')
            ->with('success', __('templates.create_success'));
    }

    public function edit(ResumeTemplate $template): View
    {
        return view(
            'admin.templates.edit',
            compact('template')
        );
    }

    public function update(
        Request $request,
        ResumeTemplate $template
    ): RedirectResponse {
        $data = $this->validatedData(
            $request,
            $template
        );

        $data['is_active'] = $request->boolean('is_active');

        $template->update($data);

        return redirect()
            ->route('admin.templates.index')
            ->with('success', __('templates.update_success'));
    }

    public function destroy(
        ResumeTemplate $template
    ): RedirectResponse {
        $inUse = ResumeVersion::query()
            ->where('template', $template->key)
            ->exists();

        if ($inUse) {
            return redirect()
                ->route('admin.templates.index')
                ->with('error', __('templates.delete_in_use'));
        }

        $template->delete();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', __('templates.delete_success'));
    }

    private function validatedData(
        Request $request,
        ?ResumeTemplate $template = null
    ): array {
        return $request->validate([
            'key' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique(
                    'resume_templates',
                    'key'
                )->ignore($template?->id),
            ],
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'description' => [
                'nullable',
                'string',
                'max:3000',
            ],
            'thumbnail' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:9999',
            ],
        ]);
    }
}
