@extends('admin.layouts.app')

@section('title', $resume->title)

@section('content')
<div class="dashboard">

    <div class="platform-hero">
        <div>
            <span class="role-badge">
                {{ __('admin_resumes.resume_details') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ $resume->title }}
            </h1>

            <p>
                {{ __('admin_resumes.resume_details_description') }}
            </p>
        </div>

        <div>
            <a
                href="{{ route('admin.resumes.index') }}"
                class="staff-btn staff-btn-secondary"
                style="text-decoration:none;"
            >
                {{ __('admin_resumes.back') }}
            </a>
        </div>
    </div>


    {{-- Main Information --}}
    <div
        class="staff-form-grid"
        style="margin-top:20px;"
    >

        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_resumes.owner') }}
            </div>

            <div
                style="
                    font-size:18px;
                    font-weight:700;
                    margin-top:8px;
                "
            >
                {{ $resume->user?->name ?? '-' }}
            </div>

            <div
                style="
                    margin-top:5px;
                    font-size:13px;
                    opacity:.65;
                "
            >
                {{ $resume->user?->email ?? '-' }}
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_resumes.status') }}
            </div>

            <div style="margin-top:10px;">
                <span class="role-badge">
                    {{ ucfirst($resume->status) }}
                </span>
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_resumes.language') }}
            </div>

            <div
                style="
                    font-size:18px;
                    font-weight:700;
                    margin-top:8px;
                "
            >
                {{ $resume->language?->name
                    ?? $resume->language?->code
                    ?? '-' }}
            </div>
        </div>


        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_resumes.current_version') }}
            </div>

            <div
                style="
                    font-size:18px;
                    font-weight:700;
                    margin-top:8px;
                "
            >
                @if($resume->currentVersion)
                    v{{ $resume->currentVersion->version_number }}
                @else
                    -
                @endif
            </div>
        </div>

    </div>


    @if($resume->currentVersion)

        {{-- Summary --}}
        <div
            class="dashboard-card"
            style="margin-top:20px;"
        >
            <h2 style="margin-top:0;">
                {{ __('admin_resumes.summary') }}
            </h2>

            <p style="line-height:1.8;">
                {{ $resume->currentVersion->summary
                    ?: __('admin_resumes.not_available') }}
            </p>
        </div>


        {{-- Current Version Statistics --}}
        <div
            class="staff-form-grid"
            style="margin-top:20px;"
        >

            <div class="dashboard-card">
                <div class="staff-form-label">
                    {{ __('admin_resumes.skills') }}
                </div>

                <div
                    style="
                        font-size:28px;
                        font-weight:800;
                        margin-top:8px;
                    "
                >
                    {{ $resume->currentVersion->skills->count() }}
                </div>
            </div>


            <div class="dashboard-card">
                <div class="staff-form-label">
                    {{ __('admin_resumes.experiences') }}
                </div>

                <div
                    style="
                        font-size:28px;
                        font-weight:800;
                        margin-top:8px;
                    "
                >
                    {{ $resume->currentVersion->experiences->count() }}
                </div>
            </div>


            <div class="dashboard-card">
                <div class="staff-form-label">
                    {{ __('admin_resumes.educations') }}
                </div>

                <div
                    style="
                        font-size:28px;
                        font-weight:800;
                        margin-top:8px;
                    "
                >
                    {{ $resume->currentVersion->educations->count() }}
                </div>
            </div>


            <div class="dashboard-card">
                <div class="staff-form-label">
                    {{ __('admin_resumes.languages') }}
                </div>

                <div
                    style="
                        font-size:28px;
                        font-weight:800;
                        margin-top:8px;
                    "
                >
                    {{ $resume->currentVersion->languages->count() }}
                </div>
            </div>

        </div>


        {{-- Skills --}}
        <div
            class="dashboard-card"
            style="margin-top:20px;"
        >
            <h2 style="margin-top:0;">
                {{ __('admin_resumes.skills') }}
            </h2>

            <div
                style="
                    display:flex;
                    flex-wrap:wrap;
                    gap:10px;
                "
            >
                @forelse($resume->currentVersion->skills as $skill)

                    <span class="role-badge">
                        {{ $skill->name }}

                        @if($skill->pivot?->level)
                            · {{ $skill->pivot->level }}
                        @endif
                    </span>

                @empty

                    <span style="opacity:.65;">
                        {{ __('admin_resumes.not_available') }}
                    </span>

                @endforelse
            </div>
        </div>


        {{-- Experience --}}
        <div
            class="dashboard-card"
            style="margin-top:20px;"
        >
            <h2 style="margin-top:0;">
                {{ __('admin_resumes.experiences') }}
            </h2>

            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin_resumes.job_title') }}</th>
                            <th>{{ __('admin_resumes.company') }}</th>
                            <th>{{ __('admin_resumes.from') }}</th>
                            <th>{{ __('admin_resumes.to') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(
                            $resume->currentVersion->experiences
                            as $experience
                        )
                            <tr>
                                <td>
                                    {{ $experience->job_title }}
                                </td>

                                <td>
                                    {{ $experience->company_name }}
                                </td>

                                <td>
                                    {{ $experience->start_date?->format('Y-m-d') }}
                                </td>

                                <td>
                                    @if($experience->is_current)
                                        {{ __('admin_resumes.current') }}
                                    @else
                                        {{ $experience->end_date?->format('Y-m-d') ?? '-' }}
                                    @endif
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4">
                                    {{ __('admin_resumes.not_available') }}
                                </td>
                            </tr>

                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- Education --}}
        <div
            class="dashboard-card"
            style="margin-top:20px;"
        >
            <h2 style="margin-top:0;">
                {{ __('admin_resumes.educations') }}
            </h2>

            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin_resumes.institution') }}</th>
                            <th>{{ __('admin_resumes.degree') }}</th>
                            <th>{{ __('admin_resumes.field') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse(
                            $resume->currentVersion->educations
                            as $education
                        )

                            <tr>
                                <td>
                                    {{ $education->institution }}
                                </td>

                                <td>
                                    {{ $education->degree ?? '-' }}
                                </td>

                                <td>
                                    {{ $education->field_of_study ?? '-' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="3">
                                    {{ __('admin_resumes.not_available') }}
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    @endif


    {{-- Versions --}}
    <div
        class="dashboard-card"
        style="margin-top:20px;"
    >
        <h2 style="margin-top:0;">
            {{ __('admin_resumes.versions') }}
        </h2>

        <div style="overflow-x:auto;">
            <table class="admin-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin_resumes.version') }}</th>
                        <th>{{ __('admin_resumes.status') }}</th>
                        <th>{{ __('admin_resumes.template') }}</th>
                        <th>{{ __('admin_resumes.created') }}</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($resume->versions as $version)

                        <tr>
                            <td>
                                {{ $version->id }}
                            </td>

                            <td>
                                <span class="role-badge">
                                    v{{ $version->version_number }}
                                </span>

                                @if(
                                    $resume->current_version_id
                                    === $version->id
                                )
                                    <span class="role-badge">
                                        {{ __('admin_resumes.current_version') }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $version->status }}
                            </td>

                            <td>
                                {{ $version->template }}
                            </td>

                            <td>
                                {{ $version->created_at?->format('Y-m-d H:i') }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5">
                                {{ __('admin_resumes.not_available') }}
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection