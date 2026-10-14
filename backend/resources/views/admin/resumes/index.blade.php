@extends('admin.layouts.app')

@section('title', __('admin_resumes.title'))

@section('content')
<div class="dashboard">

    {{-- Hero --}}
    <div class="platform-hero">
        <div>
            <span class="role-badge">
                {{ __('admin_resumes.badge') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ __('admin_resumes.management') }}
            </h1>

            <p>
                {{ __('admin_resumes.description') }}
            </p>
        </div>
    </div>


    {{-- Statistics --}}
    <div
        class="staff-form-grid"
        style="margin-top:20px;"
    >

        <div class="dashboard-card">
            <div
                style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    gap:14px;
                "
            >
                <div>
                    <div class="staff-form-label">
                        {{ __('admin_resumes.total') }}
                    </div>

                    <div
                        style="
                            font-size:32px;
                            font-weight:800;
                            margin-top:8px;
                        "
                    >
                        {{ $totalResumes }}
                    </div>
                </div>

                <div
                    style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        background:#f3f4f6;
                        display:flex;
                        align-items:center;
                        justify-content:center;
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
                        <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path>
                        <path d="M14 2v6h6"></path>
                        <path d="M8 13h8"></path>
                        <path d="M8 17h6"></path>
                    </svg>
                </div>
            </div>
        </div>


        <div class="dashboard-card">
            <div
                style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    gap:14px;
                "
            >
                <div>
                    <div class="staff-form-label">
                        {{ __('admin_resumes.active') }}
                    </div>

                    <div
                        style="
                            font-size:32px;
                            font-weight:800;
                            margin-top:8px;
                        "
                    >
                        {{ $activeResumes }}
                    </div>
                </div>

                <div
                    style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        background:#ecfdf5;
                        display:flex;
                        align-items:center;
                        justify-content:center;
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
                    justify-content:space-between;
                    align-items:center;
                    gap:14px;
                "
            >
                <div>
                    <div class="staff-form-label">
                        {{ __('admin_resumes.draft') }}
                    </div>

                    <div
                        style="
                            font-size:32px;
                            font-weight:800;
                            margin-top:8px;
                        "
                    >
                        {{ $draftResumes }}
                    </div>
                </div>

                <div
                    style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        background:#fff7ed;
                        display:flex;
                        align-items:center;
                        justify-content:center;
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
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"></path>
                    </svg>
                </div>
            </div>
        </div>


        <div class="dashboard-card">
            <div
                style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    gap:14px;
                "
            >
                <div>
                    <div class="staff-form-label">
                        {{ __('admin_resumes.total_versions') }}
                    </div>

                    <div
                        style="
                            font-size:32px;
                            font-weight:800;
                            margin-top:8px;
                        "
                    >
                        {{ $totalVersions }}
                    </div>
                </div>

                <div
                    style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        background:#eff6ff;
                        display:flex;
                        align-items:center;
                        justify-content:center;
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
                        <rect x="5" y="5" width="14" height="14" rx="2"></rect>
                        <path d="M9 5V3h10a2 2 0 0 1 2 2v10h-2"></path>
                    </svg>
                </div>
            </div>
        </div>

    </div>


    {{-- Filters --}}
    <div
        class="dashboard-card"
        style="margin-top:20px;"
    >
        <form
            method="GET"
            action="{{ route('admin.resumes.index') }}"
        >
            <div class="staff-form-grid">

                <div class="staff-form-group">
                    <label class="staff-form-label">
                        {{ __('admin_resumes.search') }}
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="staff-form-input"
                        placeholder="{{ __('admin_resumes.search_placeholder') }}"
                    >
                </div>


                <div class="staff-form-group">
                    <label class="staff-form-label">
                        {{ __('admin_resumes.status') }}
                    </label>

                    <select
                        name="status"
                        class="staff-form-select"
                    >
                        <option value="">
                            {{ __('admin_resumes.all_statuses') }}
                        </option>

                        <option
                            value="active"
                            @selected($status === 'active')
                        >
                            {{ __('admin_resumes.active') }}
                        </option>

                        <option
                            value="draft"
                            @selected($status === 'draft')
                        >
                            {{ __('admin_resumes.draft') }}
                        </option>
                    </select>
                </div>

            </div>


            <div
                class="staff-form-actions"
                style="margin-top:18px;"
            >
                <a
                    href="{{ route('admin.resumes.index') }}"
                    class="staff-btn staff-btn-secondary"
                    style="text-decoration:none;"
                >
                    {{ __('admin_resumes.reset') }}
                </a>

                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    {{ __('admin_resumes.filter') }}
                </button>
            </div>
        </form>
    </div>


    {{-- Table --}}
    <div
        class="dashboard-card"
        style="margin-top:20px;"
    >
        <div
            style="
                display:flex;
                justify-content:space-between;
                align-items:center;
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
                    {{ __('admin_resumes.all_resumes') }}
                </h2>

                <p
                    style="
                        margin:0;
                        opacity:.65;
                        font-size:14px;
                    "
                >
                    {{ __('admin_resumes.table_description') }}
                </p>
            </div>

            <span class="role-badge">
                {{ $resumes->total() }}
            </span>
        </div>


        <div style="overflow-x:auto;">
            <table class="admin-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin_resumes.resume') }}</th>
                        <th>{{ __('admin_resumes.owner') }}</th>
                        <th>{{ __('admin_resumes.language') }}</th>
                        <th>{{ __('admin_resumes.status') }}</th>
                        <th>{{ __('admin_resumes.current_version') }}</th>
                        <th>{{ __('admin_resumes.versions') }}</th>
                        <th>{{ __('admin_resumes.actions') }}</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($resumes as $resume)

                        <tr>
                            <td>
                                {{ $resume->id }}
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
                                            <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path>
                                            <path d="M14 2v6h6"></path>
                                            <path d="M8 13h8"></path>
                                            <path d="M8 17h6"></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <strong>
                                            {{ $resume->title }}
                                        </strong>

                                        <div
                                            style="
                                                margin-top:4px;
                                                font-size:12px;
                                                opacity:.65;
                                            "
                                        >
                                            {{ __('admin_resumes.created') }}
                                            {{ $resume->created_at?->format('Y-m-d') }}
                                        </div>
                                    </div>
                                </div>
                            </td>


                            <td>
                                <strong>
                                    {{ $resume->user?->name ?? '-' }}
                                </strong>

                                @if($resume->user?->email)
                                    <div
                                        style="
                                            font-size:12px;
                                            opacity:.65;
                                            margin-top:4px;
                                        "
                                    >
                                        {{ $resume->user->email }}
                                    </div>
                                @endif
                            </td>


                            <td>
                                <span class="role-badge">
                                    {{ $resume->language?->name
                                        ?? $resume->language?->code
                                        ?? '-' }}
                                </span>
                            </td>


                            <td>
                                @if($resume->status === 'active')
                                    <span
                                        class="role-badge"
                                        style="
                                            background:#ecfdf5;
                                            color:#15803d;
                                            border-color:#bbf7d0;
                                        "
                                    >
                                        ● {{ __('admin_resumes.active') }}
                                    </span>
                                @elseif($resume->status === 'draft')
                                    <span
                                        class="role-badge"
                                        style="
                                            background:#fff7ed;
                                            color:#c2410c;
                                            border-color:#fed7aa;
                                        "
                                    >
                                        ● {{ __('admin_resumes.draft') }}
                                    </span>
                                @else
                                    <span class="role-badge">
                                        {{ ucfirst($resume->status) }}
                                    </span>
                                @endif
                            </td>


                            <td>
                                @if($resume->currentVersion)
                                    <span class="role-badge">
                                        v{{ $resume->currentVersion->version_number }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>


                            <td>
                                <span class="role-badge">
                                    {{ $resume->versions_count }}
                                </span>
                            </td>


                            <td>
                                <a
                                    href="{{ route(
                                        'admin.resumes.show',
                                        $resume
                                    ) }}"
                                    class="staff-btn staff-btn-secondary"
                                    style="text-decoration:none;"
                                >
                                    {{ __('admin_resumes.view') }}
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="8"
                                style="
                                    text-align:center;
                                    padding:50px 20px;
                                "
                            >
                                {{ __('admin_resumes.no_resumes') }}
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>


        @if($resumes->hasPages())
            <div style="margin-top:20px;">
                {{ $resumes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection