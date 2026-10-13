@extends('admin.layouts.app')

@section('title', __('admin_languages.title'))

@section('content')
<div class="dashboard">

    <div
        class="platform-hero"
        style="position:relative;"
    >
        <div>
            <span class="role-badge">
                {{ __('admin_languages.badge') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ __('admin_languages.management') }}
            </h1>

            <p>
                {{ __('admin_languages.description') }}
            </p>
        </div>

        <div
            style="
                position:relative;
                z-index:30;
            "
        >
            <a
                href="{{ route('admin.languages.create') }}"
                class="staff-btn staff-btn-primary"
                style="
                    text-decoration:none;
                    display:inline-flex;
                    align-items:center;
                    gap:8px;
                "
            >
                <span>+</span>

                {{ __('admin_languages.add') }}
            </a>
        </div>
    </div>


    @if(session('success'))
        <div
            class="dashboard-card"
            style="
                margin-top:20px;
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
                {{ __('admin_languages.total') }}
            </div>

            <div
                style="
                    font-size:32px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $totalLanguages }}
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_languages.active') }}
            </div>

            <div
                style="
                    font-size:32px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $activeLanguages }}
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_languages.inactive') }}
            </div>

            <div
                style="
                    font-size:32px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $inactiveLanguages }}
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_languages.used') }}
            </div>

            <div
                style="
                    font-size:32px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $usedLanguages }}
            </div>
        </div>
    </div>


    <div
        class="dashboard-card"
        style="margin-top:20px;"
    >
        <form
            method="GET"
            action="{{ route('admin.languages.index') }}"
        >
            <div class="staff-form-grid">

                <div class="staff-form-group">
                    <label class="staff-form-label">
                        {{ __('admin_languages.search') }}
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="staff-form-input"
                        placeholder="{{ __('admin_languages.search_placeholder') }}"
                    >
                </div>


                <div class="staff-form-group">
                    <label class="staff-form-label">
                        {{ __('admin_languages.status') }}
                    </label>

                    <select
                        name="status"
                        class="staff-form-select"
                    >
                        <option value="">
                            {{ __('admin_languages.all_statuses') }}
                        </option>

                        <option
                            value="active"
                            @selected($status === 'active')
                        >
                            {{ __('admin_languages.active') }}
                        </option>

                        <option
                            value="inactive"
                            @selected($status === 'inactive')
                        >
                            {{ __('admin_languages.inactive') }}
                        </option>
                    </select>
                </div>

            </div>

            <div
                class="staff-form-actions"
                style="margin-top:18px;"
            >
                <a
                    href="{{ route('admin.languages.index') }}"
                    class="staff-btn staff-btn-secondary"
                    style="text-decoration:none;"
                >
                    {{ __('admin_languages.reset') }}
                </a>

                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    {{ __('admin_languages.filter') }}
                </button>
            </div>
        </form>
    </div>


    <div
        class="dashboard-card"
        style="margin-top:20px;"
    >
        <div style="overflow-x:auto;">
            <table class="admin-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin_languages.language') }}</th>
                        <th>{{ __('admin_languages.code') }}</th>
                        <th>{{ __('admin_languages.direction') }}</th>
                        <th>{{ __('admin_languages.status') }}</th>
                        <th>{{ __('admin_languages.writing_usage') }}</th>
                        <th>{{ __('admin_languages.spoken_usage') }}</th>
                        <th>{{ __('admin_languages.actions') }}</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($languages as $language)
                        <tr>
                            <td>
                                {{ $language->id }}
                            </td>

                            <td>
                                <strong>
                                    {{ $language->name }}
                                </strong>

                                @if($language->native_name)
                                    <div
                                        style="
                                            font-size:12px;
                                            opacity:.65;
                                            margin-top:4px;
                                        "
                                    >
                                        {{ $language->native_name }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ $language->code ?: '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ strtoupper(
                                        $language->direction
                                    ) }}
                                </span>
                            </td>

                            <td>
                                @if($language->is_active)
                                    <span
                                        class="role-badge"
                                        style="
                                            color:#15803d;
                                            background:#ecfdf5;
                                            border-color:#bbf7d0;
                                        "
                                    >
                                        ● {{ __('admin_languages.active') }}
                                    </span>
                                @else
                                    <span
                                        class="role-badge"
                                        style="
                                            color:#b91c1c;
                                            background:#fef2f2;
                                            border-color:#fecaca;
                                        "
                                    >
                                        ● {{ __('admin_languages.inactive') }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ $language->resumes_count }}
                                </span>
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ $language->resume_versions_count }}
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
                                            'admin.languages.edit',
                                            $language
                                        ) }}"
                                        class="staff-btn staff-btn-secondary"
                                        style="text-decoration:none;"
                                    >
                                        {{ __('admin_languages.edit') }}
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.languages.destroy',
                                            $language
                                        ) }}"
                                        onsubmit="return confirm('{{ __('admin_languages.delete_confirm') }}');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="staff-btn staff-btn-secondary"
                                        >
                                            {{ __('admin_languages.delete') }}
                                        </button>
                                    </form>
                                </div>
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
                                {{ __('admin_languages.no_languages') }}
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>


        @if($languages->hasPages())
            <div style="margin-top:20px;">
                {{ $languages->links() }}
            </div>
        @endif
    </div>

</div>
@endsection