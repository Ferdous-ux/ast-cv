@extends('admin.layouts.app')

@section('title', __('admin_skills.title'))

@section('content')
<div class="dashboard">

    <div
        class="platform-hero"
        style="position:relative;"
    >
        <div>
            <span class="role-badge">
                {{ __('admin_skills.badge') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ __('admin_skills.management') }}
            </h1>

            <p>
                {{ __('admin_skills.description') }}
            </p>
        </div>

        <div
            style="
                position:relative;
                z-index:30;
            "
        >
            <a
                href="{{ route('admin.skills.create') }}"
                class="staff-btn staff-btn-primary"
                style="
                    display:inline-flex;
                    align-items:center;
                    gap:8px;
                    text-decoration:none;
                    pointer-events:auto;
                "
            >
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>

                {{ __('admin_skills.add') }}
            </a>
        </div>
    </div>


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


    <div
        class="staff-form-grid"
        style="margin-top:20px;"
    >

        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_skills.total') }}
            </div>

            <div
                style="
                    font-size:32px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $totalSkills }}
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_skills.total_aliases') }}
            </div>

            <div
                style="
                    font-size:32px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $totalAliases }}
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_skills.categories') }}
            </div>

            <div
                style="
                    font-size:32px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $categoriesCount }}
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_skills.used_skills') }}
            </div>

            <div
                style="
                    font-size:32px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $usedSkills }}
            </div>
        </div>

    </div>


    <div
        class="dashboard-card"
        style="margin-top:20px;"
    >
        <form
            method="GET"
            action="{{ route('admin.skills.index') }}"
        >
            <div class="staff-form-grid">

                <div class="staff-form-group">
                    <label class="staff-form-label">
                        {{ __('admin_skills.search') }}
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="staff-form-input"
                        placeholder="{{ __('admin_skills.search_placeholder') }}"
                    >
                </div>


                <div class="staff-form-group">
                    <label class="staff-form-label">
                        {{ __('admin_skills.category') }}
                    </label>

                    <select
                        name="category"
                        class="staff-form-select"
                    >
                        <option value="">
                            {{ __('admin_skills.all_categories') }}
                        </option>

                        @foreach($categories as $item)
                            <option
                                value="{{ $item }}"
                                @selected($category === $item)
                            >
                                {{ $item }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div
                class="staff-form-actions"
                style="margin-top:18px;"
            >
                <a
                    href="{{ route('admin.skills.index') }}"
                    class="staff-btn staff-btn-secondary"
                    style="text-decoration:none;"
                >
                    {{ __('admin_skills.reset') }}
                </a>

                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    {{ __('admin_skills.filter') }}
                </button>
            </div>
        </form>
    </div>


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
                    {{ __('admin_skills.all_skills') }}
                </h2>

                <p
                    style="
                        margin:0;
                        opacity:.65;
                        font-size:14px;
                    "
                >
                    {{ __('admin_skills.table_description') }}
                </p>
            </div>

            <span class="role-badge">
                {{ $skills->total() }}
            </span>
        </div>


        <div style="overflow-x:auto;">
            <table class="admin-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin_skills.name') }}</th>
                        <th>{{ __('admin_skills.category') }}</th>
                        <th>{{ __('admin_skills.aliases') }}</th>
                        <th>{{ __('admin_skills.resume_usage') }}</th>
                        <th>{{ __('admin_skills.job_usage') }}</th>
                        <th>{{ __('admin_skills.actions') }}</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($skills as $skill)

                        <tr>
                            <td>
                                {{ $skill->id }}
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
                                            width:42px;
                                            height:42px;
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
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <circle cx="12" cy="12" r="5"></circle>
                                            <circle cx="12" cy="12" r="1"></circle>
                                        </svg>
                                    </div>

                                    <strong>
                                        {{ $skill->name }}
                                    </strong>
                                </div>
                            </td>


                            <td>
                                @if($skill->category)
                                    <span class="role-badge">
                                        {{ $skill->category }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>


                            <td>
                                <span class="role-badge">
                                    {{ $skill->aliases_count }}
                                </span>
                            </td>


                            <td>
                                <span class="role-badge">
                                    {{ $skill->resume_versions_count }}
                                </span>
                            </td>


                            <td>
                                <span class="role-badge">
                                    {{ $skill->jobs_count }}
                                </span>
                            </td>


                            <td>
                                <div
                                    style="
                                        display:flex;
                                        gap:8px;
                                        flex-wrap:wrap;
                                    "
                                >
                                    <a
                                        href="{{ route(
                                            'admin.skills.edit',
                                            $skill
                                        ) }}"
                                        class="staff-btn staff-btn-secondary"
                                        style="text-decoration:none;"
                                    >
                                        {{ __('admin_skills.edit') }}
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.skills.destroy',
                                            $skill
                                        ) }}"
                                        onsubmit="return confirm('{{ __('admin_skills.delete_confirm') }}');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="staff-btn staff-btn-secondary"
                                        >
                                            {{ __('admin_skills.delete') }}
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
                                    text-align:center;
                                    padding:50px 20px;
                                "
                            >
                                {{ __('admin_skills.no_skills') }}
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>


        @if($skills->hasPages())
            <div style="margin-top:20px;">
                {{ $skills->links() }}
            </div>
        @endif
    </div>

</div>
@endsection