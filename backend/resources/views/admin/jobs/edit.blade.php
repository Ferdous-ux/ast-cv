@extends('admin.layouts.app')

@section('title', __('jobs.edit_title'))

@section('page-title')
{{ __('jobs.edit_title') }}
@endsection

@section('page-description')
{{ __('jobs.edit_description') }}
@endsection

@section('content')

@php
    $attachedSkills = $job->skills->keyBy('id');

    $selectedSkillIds = collect(
        old(
            'skill_ids',
            $job->skills->pluck('id')->all()
        )
    )->map(fn ($id) => (int) $id)->all();
@endphp

<div class="dashboard">

<section class="dashboard-card staff-form-card">

<div class="dashboard-card__header">
    <div>
        <h3>{{ __('jobs.edit_title') }}</h3>
        <p>{{ __('jobs.edit_description') }}</p>
    </div>
</div>

<form
    method="POST"
    action="{{ route('admin.jobs.update', $job->id) }}"
>

@csrf
@method('PUT')

<div class="staff-form-grid">

<div class="staff-form-group">
    <label class="staff-form-label">
        {{ __('jobs.title_field') }}
    </label>

    <input
        type="text"
        name="title"
        class="staff-form-input"
        value="{{ old('title', $job->title) }}"
        required
    >
</div>

<div class="staff-form-group">
    <label class="staff-form-label">
        {{ __('jobs.company_name') }}
    </label>

    <input
        type="text"
        name="company_name"
        class="staff-form-input"
        value="{{ old('company_name', $job->company_name) }}"
    >
</div>

<div class="staff-form-group">
    <label class="staff-form-label">
        {{ __('jobs.location') }}
    </label>

    <input
        type="text"
        name="location"
        class="staff-form-input"
        value="{{ old('location', $job->location) }}"
    >
</div>

<div class="staff-form-group">
    <label class="staff-form-label">
        {{ __('jobs.minimum_experience_years') }}
    </label>

    <input
        type="number"
        name="minimum_experience_years"
        class="staff-form-input"
        value="{{ old('minimum_experience_years', $job->minimum_experience_years) }}"
        min="0"
        max="50"
    >
</div>

<div class="staff-form-group">
    <label class="staff-form-label">
        {{ __('jobs.employment_type') }}
    </label>

    <select name="employment_type" class="staff-form-select">

        <option value="">
            {{ __('jobs.select') }}
        </option>

        @foreach([
            'full_time',
            'part_time',
            'contract',
            'internship',
            'temporary'
        ] as $type)

            <option
                value="{{ $type }}"
                {{ old('employment_type', $job->employment_type) === $type ? 'selected' : '' }}
            >
                {{ __('jobs.'.$type) }}
            </option>

        @endforeach

    </select>
</div>

<div class="staff-form-group">
    <label class="staff-form-label">
        {{ __('jobs.workplace_type') }}
    </label>

    <select name="workplace_type" class="staff-form-select">

        <option value="">
            {{ __('jobs.select') }}
        </option>

        @foreach(['onsite', 'remote', 'hybrid'] as $type)

            <option
                value="{{ $type }}"
                {{ old('workplace_type', $job->workplace_type) === $type ? 'selected' : '' }}
            >
                {{ __('jobs.'.$type) }}
            </option>

        @endforeach

    </select>
</div>

<div class="staff-form-group">
    <label class="staff-form-label">
        {{ __('jobs.education_level') }}
    </label>

    <input
        type="text"
        name="education_level"
        class="staff-form-input"
        value="{{ old('education_level', $job->education_level) }}"
    >
</div>

<div class="staff-form-group">
    <label class="staff-form-label">
        {{ __('jobs.status') }}
    </label>

    <select
        name="status"
        class="staff-form-select"
        required
    >

        @foreach(['draft', 'active', 'closed'] as $status)

            <option
                value="{{ $status }}"
                {{ old('status', $job->status) === $status ? 'selected' : '' }}
            >
                {{ __('jobs.'.$status) }}
            </option>

        @endforeach

    </select>
</div>

<div class="staff-form-group full">
    <label class="staff-form-label">
        {{ __('jobs.description_field') }}
    </label>

    <textarea
        name="description"
        class="staff-form-input"
        rows="7"
        style="height:auto;padding-top:14px;padding-bottom:14px;"
        required
    >{{ old('description', $job->description) }}</textarea>
</div>

<div class="staff-form-group full">
    <label class="staff-form-label">
        {{ __('jobs.requirements') }}
    </label>

    <textarea
        name="requirements"
        class="staff-form-input"
        rows="6"
        style="height:auto;padding-top:14px;padding-bottom:14px;"
    >{{ old('requirements', $job->requirements) }}</textarea>
</div>

<div class="staff-form-group full">

    <label class="staff-form-label">
        {{ __('jobs.skills') }}
    </label>

    <p style="margin:0;color:var(--color-text-muted);font-size:13px;">
        {{ __('jobs.skill_configuration_help') }}
    </p>

    @error('skill_ids')
        <span class="form-error">{{ $message }}</span>
    @enderror

    <div style="display:flex;flex-direction:column;gap:12px;margin-top:10px;">

        @foreach($skills as $skill)

            @php
                $attachedSkill = $attachedSkills->get($skill->id);

                $selected = in_array(
                    $skill->id,
                    $selectedSkillIds,
                    true
                );

                $importance = old(
                    'importance.'.$skill->id,
                    $attachedSkill?->pivot?->importance ?? 'required'
                );

                $weight = old(
                    'weight.'.$skill->id,
                    $attachedSkill?->pivot?->weight ?? 100
                );

                $minimumLevel = old(
                    'minimum_level.'.$skill->id,
                    $attachedSkill?->pivot?->minimum_level
                );
            @endphp

            <div
                style="
                    border:1px solid var(--color-border-light);
                    border-radius:14px;
                    padding:14px;
                    background:var(--color-surface-soft);
                    overflow-x:auto;
                "
            >

                <div
                    style="
                        display:grid;
                        grid-template-columns:minmax(180px,1fr) 160px 120px 180px;
                        gap:12px;
                        align-items:center;
                        min-width:720px;
                    "
                >

                    <label style="display:flex;align-items:center;gap:10px;font-weight:600;">

                        <input
                            type="checkbox"
                            name="skill_ids[]"
                            value="{{ $skill->id }}"
                            {{ $selected ? 'checked' : '' }}
                        >

                        <span>
                            {{ $skill->name }}

                            @if($skill->category)
                                <small style="display:block;font-weight:400;color:var(--color-text-muted);">
                                    {{ $skill->category }}
                                </small>
                            @endif
                        </span>

                    </label>

                    <select
                        name="importance[{{ $skill->id }}]"
                        class="staff-form-select"
                    >
                        <option
                            value="required"
                            {{ $importance === 'required' ? 'selected' : '' }}
                        >
                            {{ __('jobs.required_skill') }}
                        </option>

                        <option
                            value="preferred"
                            {{ $importance === 'preferred' ? 'selected' : '' }}
                        >
                            {{ __('jobs.preferred_skill') }}
                        </option>
                    </select>

                    <input
                        type="number"
                        name="weight[{{ $skill->id }}]"
                        class="staff-form-input"
                        value="{{ $weight }}"
                        min="1"
                        max="100"
                    >

                    <select
                        name="minimum_level[{{ $skill->id }}]"
                        class="staff-form-select"
                    >

                        <option value="">
                            {{ __('jobs.no_minimum_level') }}
                        </option>

                        @foreach([
                            'beginner',
                            'intermediate',
                            'advanced',
                            'expert'
                        ] as $level)

                            <option
                                value="{{ $level }}"
                                {{ $minimumLevel === $level ? 'selected' : '' }}
                            >
                                {{ __('jobs.'.$level) }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        @endforeach

    </div>

</div>

</div>

<div class="staff-form-actions">

    <a
        href="{{ route('admin.jobs.index') }}"
        class="staff-btn staff-btn-secondary"
    >
        {{ __('jobs.cancel') }}
    </a>

    <button
        type="submit"
        class="staff-btn staff-btn-primary"
    >
        {{ __('jobs.update') }}
    </button>

</div>

</form>

</section>

</div>

@endsection