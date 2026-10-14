@extends('admin.layouts.app')

@section('title', __('matching.title'))

@section('page-title')
{{ __('matching.title') }}
@endsection

@section('page-description')
{{ __('matching.management') }}
@endsection

@section('content')

<div class="dashboard staff-page">

    <section class="platform-hero">

        <div class="platform-hero__content">

            <span class="platform-badge">
                {{ __('matching.title') }}
            </span>

            <h2>
                {{ __('matching.management') }}
            </h2>

            <p>
                {{ __('matching.description') }}
            </p>

        </div>

        <div class="platform-hero__meta">

            <div class="platform-stat">
                <span>
                    {{ __('matching.current_resumes') }}
                </span>

                <strong>
                    {{ $totalCurrentResumes }}
                </strong>
            </div>

            <div class="platform-stat">
                <span>
                    {{ __('matching.active_jobs') }}
                </span>

                <strong>
                    {{ $totalActiveJobs }}
                </strong>
            </div>

        </div>

    </section>


    <div class="staff-form-grid"
         style="margin-top:20px;">


        {{-- Resume -> Jobs --}}
        <section class="dashboard-card staff-form-card">

            <div class="dashboard-card__header">

                <div>

                    <h3>
                        {{ __('matching.resume_to_jobs') }}
                    </h3>

                    <p>
                        {{ __('matching.resume_to_jobs_description') }}
                    </p>

                </div>

            </div>

            <form
                method="POST"
                action="{{ route('admin.matching.run') }}"
            >

                @csrf

                <input
                    type="hidden"
                    name="mode"
                    value="resume_to_jobs"
                >

                <div class="staff-form-group">

                    <label class="staff-form-label">
                        {{ __('matching.resume') }}
                    </label>

                    <select
                        name="resume_version_id"
                        class="staff-form-select"
                        required
                    >

                        <option value="">
                            {{ __('matching.select_resume') }}
                        </option>

                        @foreach($resumeVersions as $version)

                            <option
                                value="{{ $version->id }}"
                                {{ old('resume_version_id') == $version->id ? 'selected' : '' }}
                            >

                                {{ $version->resume?->title ?? '-' }}

                                - V{{ $version->version_number }}

                                @if($version->resume?->user?->name)
                                    - {{ $version->resume->user->name }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('resume_version_id')
                        <span class="form-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                <div class="staff-form-group">

                    <label class="staff-form-label">
                        {{ __('matching.result_limit') }}
                    </label>

                    <select
                        name="limit"
                        class="staff-form-select"
                    >

                        @foreach([5, 10, 20, 50] as $limit)

                            <option
                                value="{{ $limit }}"
                                {{ old('limit', 10) == $limit ? 'selected' : '' }}
                            >
                                {{ $limit }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="staff-form-actions">

                    <button
                        type="submit"
                        class="staff-btn staff-btn-primary"
                    >
                        {{ __('matching.find_jobs') }}
                    </button>

                </div>

            </form>

        </section>


        {{-- Job -> Resumes --}}
        <section class="dashboard-card staff-form-card">

            <div class="dashboard-card__header">

                <div>

                    <h3>
                        {{ __('matching.job_to_resumes') }}
                    </h3>

                    <p>
                        {{ __('matching.job_to_resumes_description') }}
                    </p>

                </div>

            </div>

            <form
                method="POST"
                action="{{ route('admin.matching.run') }}"
            >

                @csrf

                <input
                    type="hidden"
                    name="mode"
                    value="job_to_resumes"
                >

                <div class="staff-form-group">

                    <label class="staff-form-label">
                        {{ __('matching.job') }}
                    </label>

                    <select
                        name="job_id"
                        class="staff-form-select"
                        required
                    >

                        <option value="">
                            {{ __('matching.select_job') }}
                        </option>

                        @foreach($jobs as $job)

                            <option
                                value="{{ $job->id }}"
                                {{ old('job_id') == $job->id ? 'selected' : '' }}
                            >

                                {{ $job->title }}

                                @if($job->company_name)
                                    - {{ $job->company_name }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('job_id')
                        <span class="form-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>


                <div class="staff-form-group">

                    <label class="staff-form-label">
                        {{ __('matching.result_limit') }}
                    </label>

                    <select
                        name="limit"
                        class="staff-form-select"
                    >

                        @foreach([5, 10, 20, 50] as $limit)

                            <option
                                value="{{ $limit }}"
                                {{ old('limit', 10) == $limit ? 'selected' : '' }}
                            >
                                {{ $limit }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="staff-form-actions">

                    <button
                        type="submit"
                        class="staff-btn staff-btn-primary"
                    >
                        {{ __('matching.find_resumes') }}
                    </button>

                </div>

            </form>

        </section>

    </div>

</div>

@endsection