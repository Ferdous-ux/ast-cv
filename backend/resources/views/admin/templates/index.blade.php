@extends('admin.layouts.app')

@section('title', __('templates.title'))

@section('content')
<div class="dashboard">

    {{-- Hero --}}
    <div
        class="platform-hero"
        style="
            position:relative;
            overflow:hidden;
        "
    >
        <div
            style="
                position:relative;
                z-index:2;
            "
        >
            <span class="role-badge">
                {{ __('templates.badge') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ __('templates.management') }}
            </h1>

            <p>
                {{ __('templates.description') }}
            </p>
        </div>

        <div
            style="
                position:relative;
                z-index:20;
                pointer-events:auto;
            "
        >
            <a
                href="{{ route('admin.templates.create') }}"
                class="staff-btn staff-btn-primary"
                style="
                    display:inline-flex;
                    align-items:center;
                    justify-content:center;
                    gap:8px;
                    min-height:44px;
                    padding:0 18px;
                    position:relative;
                    z-index:50;
                    pointer-events:auto;
                    cursor:pointer;
                    text-decoration:none;
                "
            >
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>

                <span>
                    {{ __('templates.add') }}
                </span>
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div
            class="dashboard-card"
            style="
                margin-top:20px;
                padding:16px 18px;
                border-inline-start:4px solid #16a34a;
            "
        >
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div
            class="dashboard-card"
            style="
                margin-top:20px;
                padding:16px 18px;
                border-inline-start:4px solid #dc2626;
            "
        >
            {{ session('error') }}
        </div>
    @endif

    {{-- Statistics --}}
    <div
        class="staff-form-grid"
        style="margin-top:20px;"
    >
        <div class="dashboard-card">
            <div
                style="
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                    gap:14px;
                "
            >
                <div>
                    <div class="staff-form-label">
                        {{ __('templates.total_templates') }}
                    </div>

                    <div
                        style="
                            margin-top:8px;
                            font-size:32px;
                            line-height:1;
                            font-weight:800;
                        "
                    >
                        {{ $totalTemplates }}
                    </div>
                </div>

                <div
                    style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#f3f4f6;
                        flex-shrink:0;
                    "
                >
                    <svg
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                    </svg>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div
                style="
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                    gap:14px;
                "
            >
                <div>
                    <div class="staff-form-label">
                        {{ __('templates.active_templates') }}
                    </div>

                    <div
                        style="
                            margin-top:8px;
                            font-size:32px;
                            line-height:1;
                            font-weight:800;
                        "
                    >
                        {{ $activeTemplates }}
                    </div>
                </div>

                <div
                    style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#ecfdf5;
                        flex-shrink:0;
                    "
                >
                    <svg
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div
                style="
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                    gap:14px;
                "
            >
                <div>
                    <div class="staff-form-label">
                        {{ __('templates.inactive_templates') }}
                    </div>

                    <div
                        style="
                            margin-top:8px;
                            font-size:32px;
                            line-height:1;
                            font-weight:800;
                        "
                    >
                        {{ $inactiveTemplates }}
                    </div>
                </div>

                <div
                    style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#fef2f2;
                        flex-shrink:0;
                    "
                >
                    <svg
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M9 9l6 6"></path>
                        <path d="M15 9l-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Search / Filters --}}
    <div
        class="dashboard-card"
        style="margin-top:20px;"
    >
        <div style="margin-bottom:18px;">
            <h2
                style="
                    margin:0 0 5px;
                    font-size:18px;
                "
            >
                {{ __('templates.search') }}
            </h2>

            <p
                style="
                    margin:0;
                    opacity:.65;
                    font-size:14px;
                "
            >
                {{ __('templates.search_placeholder') }}
            </p>
        </div>

        <form
            method="GET"
            action="{{ route('admin.templates.index') }}"
        >
            <div class="staff-form-grid">
                <div class="staff-form-group">
                    <label class="staff-form-label">
                        {{ __('templates.search') }}
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="staff-form-input"
                        placeholder="{{ __('templates.search_placeholder') }}"
                    >
                </div>

                <div class="staff-form-group">
                    <label class="staff-form-label">
                        {{ __('templates.status') }}
                    </label>

                    <select
                        name="status"
                        class="staff-form-select"
                    >
                        <option value="">
                            {{ __('templates.all_statuses') }}
                        </option>

                        <option
                            value="active"
                            @selected($status === 'active')
                        >
                            {{ __('templates.active') }}
                        </option>

                        <option
                            value="inactive"
                            @selected($status === 'inactive')
                        >
                            {{ __('templates.inactive') }}
                        </option>
                    </select>
                </div>
            </div>

            <div
                class="staff-form-actions"
                style="margin-top:18px;"
            >
                <a
                    href="{{ route('admin.templates.index') }}"
                    class="staff-btn staff-btn-secondary"
                    style="text-decoration:none;"
                >
                    {{ app()->getLocale() === 'ar'
                        ? 'إعادة تعيين'
                        : 'Reset' }}
                </a>

                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    {{ __('templates.filter') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Templates List --}}
    <div
        class="dashboard-card"
        style="margin-top:20px;"
    >
        <div
            style="
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:16px;
                flex-wrap:wrap;
                margin-bottom:18px;
            "
        >
            <div>
                <h2
                    style="
                        margin:0 0 5px;
                        font-size:18px;
                    "
                >
                    {{ __('templates.title') }}
                </h2>

                <p
                    style="
                        margin:0;
                        opacity:.65;
                        font-size:14px;
                    "
                >
                    {{ __('templates.description') }}
                </p>
            </div>

            <span class="role-badge">
                {{ $templates->total() }}
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('templates.name') }}</th>
                        <th>{{ __('templates.key') }}</th>
                        <th>{{ __('templates.status') }}</th>
                        <th>{{ __('templates.sort_order') }}</th>
                        <th>{{ __('templates.usage') }}</th>
                        <th>{{ __('templates.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td>
                                {{ $template->id }}
                            </td>

                            <td>
                                <div
                                    style="
                                        display:flex;
                                        align-items:center;
                                        gap:12px;
                                    "
                                >
                                    <div
                                        style="
                                            width:44px;
                                            height:44px;
                                            border-radius:12px;
                                            background:#f3f4f6;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            flex-shrink:0;
                                        "
                                    >
                                        <svg
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path d="M4 3h16v18H4z"></path>
                                            <path d="M8 7h8"></path>
                                            <path d="M8 11h8"></path>
                                            <path d="M8 15h5"></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <strong>
                                            {{ $template->name }}
                                        </strong>

                                        @if($template->description)
                                            <div
                                                style="
                                                    margin-top:4px;
                                                    max-width:320px;
                                                    font-size:12px;
                                                    opacity:.65;
                                                "
                                            >
                                                {{ \Illuminate\Support\Str::limit(
                                                    $template->description,
                                                    90
                                                ) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ $template->key }}
                                </span>
                            </td>

                            <td>
                                @if($template->is_active)
                                    <span
                                        class="role-badge"
                                        style="
                                            background:#ecfdf5;
                                            color:#15803d;
                                            border-color:#bbf7d0;
                                        "
                                    >
                                        ● {{ __('templates.active') }}
                                    </span>
                                @else
                                    <span
                                        class="role-badge"
                                        style="
                                            background:#fef2f2;
                                            color:#b91c1c;
                                            border-color:#fecaca;
                                        "
                                    >
                                        ● {{ __('templates.inactive') }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $template->sort_order }}
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ $template->resume_versions_count }}
                                </span>
                            </td>

                            <td>
                                <div
                                    style="
                                        display:flex;
                                        align-items:center;
                                        gap:8px;
                                        flex-wrap:wrap;
                                    "
                                >
                                    <a
                                        href="{{ route(
                                            'admin.templates.edit',
                                            $template
                                        ) }}"
                                        class="staff-btn staff-btn-secondary"
                                        style="text-decoration:none;"
                                    >
                                        {{ __('templates.edit') }}
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.templates.destroy',
                                            $template
                                        ) }}"
                                        onsubmit="return confirm('{{ __('templates.delete_confirm') }}');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="staff-btn staff-btn-secondary"
                                        >
                                            {{ __('templates.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="7"
                                style="
                                    padding:55px 20px;
                                    text-align:center;
                                "
                            >
                                <div
                                    style="
                                        width:58px;
                                        height:58px;
                                        margin:0 auto 14px;
                                        border-radius:16px;
                                        background:#f3f4f6;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                    "
                                >
                                    <svg
                                        width="26"
                                        height="26"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M4 3h16v18H4z"></path>
                                        <path d="M8 7h8"></path>
                                        <path d="M8 11h8"></path>
                                        <path d="M8 15h5"></path>
                                    </svg>
                                </div>

                                <div
                                    style="
                                        font-size:16px;
                                        font-weight:700;
                                    "
                                >
                                    {{ __('templates.no_templates') }}
                                </div>

                                <a
                                    href="{{ route('admin.templates.create') }}"
                                    class="staff-btn staff-btn-primary"
                                    style="
                                        display:inline-flex;
                                        margin-top:16px;
                                        position:relative;
                                        z-index:20;
                                        pointer-events:auto;
                                        text-decoration:none;
                                    "
                                >
                                    {{ __('templates.add') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div style="margin-top:20px;">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
