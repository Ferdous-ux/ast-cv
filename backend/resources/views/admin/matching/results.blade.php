@extends('admin.layouts.app')

@section('title', __('matching.results'))

@section('page-title')
{{ __('matching.results') }}
@endsection

@section('page-description')
{{ __('matching.results_description') }}
@endsection

@section('content')

<div class="dashboard staff-page">

    <section class="platform-hero">

        <div class="platform-hero__content">

            <span class="platform-badge">
                {{ __('matching.title') }}
            </span>

            @if($mode === 'resume_to_jobs')

                <h2>
                    {{ __('matching.best_jobs') }}
                </h2>

                <p>
                    {{ __('matching.resume') }}:
                    <strong>
                        {{ $resumeVersion?->resume?->title ?? '-' }}
                    </strong>

                    · V{{ $resumeVersion?->version_number }}

                    @if($resumeVersion?->resume?->user?->name)
                        ·
                        {{ $resumeVersion->resume->user->name }}
                    @endif
                </p>

            @else

                <h2>
                    {{ __('matching.best_resumes') }}
                </h2>

                <p>
                    {{ __('matching.job') }}:
                    <strong>
                        {{ $job?->title ?? '-' }}
                    </strong>

                    @if($job?->company_name)
                        · {{ $job->company_name }}
                    @endif
                </p>

            @endif

        </div>

        <div class="platform-hero__meta">

            <div class="platform-stat">

                <span>
                    {{ __('matching.results_count') }}
                </span>

                <strong>
                    {{ $results->count() }}
                </strong>

            </div>

        </div>

    </section>


    <section
        class="dashboard-card"
        style="margin-top:20px;"
    >

        <div class="dashboard-card__header">

            <div>

                <h3>
                    {{ __('matching.ranking') }}
                </h3>

                <p>
                    {{ __('matching.ranking_description') }}
                </p>

            </div>

            <a
                href="{{ route('admin.matching.index') }}"
                class="staff-btn staff-btn-secondary"
            >
                {{ __('matching.new_matching') }}
            </a>

        </div>


        <div class="table-wrapper staff-table-wrapper">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>
                            {{ __('matching.rank') }}
                        </th>

                        <th>
                            {{ __('matching.resume') }}
                        </th>

                        <th>
                            {{ __('matching.job') }}
                        </th>

                        <th>
                            {{ __('matching.overall') }}
                        </th>

                        <th>
                            {{ __('matching.skills') }}
                        </th>

                        <th>
                            {{ __('matching.experience') }}
                        </th>

                        <th>
                            {{ __('matching.education') }}
                        </th>

                        <th>
                            {{ __('matching.keywords') }}
                        </th>

                        <th>
                            {{ __('matching.skill_match') }}
                        </th>

                        <th>
                            {{ __('matching.actions') }}
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($results as $index => $analysis)

                        @php
                            $matchedSkills =
                                is_array($analysis->matched_skills ?? null)
                                    ? $analysis->matched_skills
                                    : [];

                            $missingSkills =
                                is_array($analysis->missing_skills ?? null)
                                    ? $analysis->missing_skills
                                    : [];
                        @endphp

                        <tr>

                            <td>
                                <strong>
                                    #{{ $index + 1 }}
                                </strong>
                            </td>

                            <td>

                                {{ $analysis
                                    ->resumeVersion
                                    ?->resume
                                    ?->title ?? '-' }}

                                @if(
                                    $analysis
                                        ->resumeVersion
                                        ?->resume
                                        ?->user
                                        ?->name
                                )

                                    <div
                                        style="
                                            font-size:12px;
                                            opacity:.7;
                                            margin-top:4px;
                                        "
                                    >
                                        {{ $analysis
                                            ->resumeVersion
                                            ->resume
                                            ->user
                                            ->name }}
                                    </div>

                                @endif

                            </td>

                            <td>

                                {{ $analysis->job?->title ?? '-' }}

                                @if($analysis->job?->company_name)

                                    <div
                                        style="
                                            font-size:12px;
                                            opacity:.7;
                                            margin-top:4px;
                                        "
                                    >
                                        {{ $analysis->job->company_name }}
                                    </div>

                                @endif

                            </td>

                            <td>

                                <span class="role-badge">

                                    {{ number_format(
                                        (float) $analysis->overall_score,
                                        1
                                    ) }}%

                                </span>

                            </td>

                            <td>
                                {{ number_format(
                                    (float) $analysis->skills_score,
                                    1
                                ) }}%
                            </td>

                            <td>
                                {{ number_format(
                                    (float) $analysis->experience_score,
                                    1
                                ) }}%
                            </td>

                            <td>
                                {{ number_format(
                                    (float) $analysis->education_score,
                                    1
                                ) }}%
                            </td>

                            <td>
                                {{ number_format(
                                    (float) $analysis->keywords_score,
                                    1
                                ) }}%
                            </td>

                            <td>

                                <span class="role-badge">
                                    ✓ {{ count($matchedSkills) }}
                                </span>

                                <span class="role-badge">
                                    ✕ {{ count($missingSkills) }}
                                </span>

                            </td>

                            <td>

                                <a
                                    href="{{ route(
                                        'admin.ats.show',
                                        $analysis
                                    ) }}"
                                    class="action-edit"
                                >
                                    {{ __('matching.view_analysis') }}
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="10"
                                style="text-align:center;"
                            >
                                {{ __('matching.no_results') }}
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

@endsection