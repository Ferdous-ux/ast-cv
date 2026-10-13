@extends('admin.layouts.app')

@section('title', __('jobs.show_title'))

@section('page-title')
{{ __('jobs.show_title') }}
@endsection

@section('page-description')
{{ $job->title }}
@endsection

@section('content')

<div class="dashboard">

<section class="dashboard-card staff-form-card">

<div class="dashboard-card__header">

<div>
<h3>{{ $job->title }}</h3>
<p>{{ $job->company_name ?: '-' }}</p>
</div>

<a
href="{{ route('admin.jobs.edit', $job->id) }}"
class="staff-btn staff-btn-primary"
>
{{ __('jobs.edit') }}
</a>

</div>

<div class="staff-form-grid">

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('jobs.company_name') }}
</label>

<strong>
{{ $job->company_name ?: '-' }}
</strong>

</div>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('jobs.location') }}
</label>

<strong>
{{ $job->location ?: '-' }}
</strong>

</div>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('jobs.employment_type') }}
</label>

<strong>
{{ $job->employment_type ? __('jobs.'.$job->employment_type) : '-' }}
</strong>

</div>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('jobs.workplace_type') }}
</label>

<strong>
{{ $job->workplace_type ? __('jobs.'.$job->workplace_type) : '-' }}
</strong>

</div>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('jobs.minimum_experience_years') }}
</label>

<strong>
{{ $job->minimum_experience_years ?? '-' }}
</strong>

</div>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('jobs.education_level') }}
</label>

<strong>
{{ $job->education_level ?: '-' }}
</strong>

</div>

<div class="staff-form-group full">

<label class="staff-form-label">
{{ __('jobs.skills') }}
</label>

<div class="roles-box">

@forelse($job->skills as $skill)

<span class="role-badge">
{{ $skill->name }}
</span>

@empty

<span>
{{ __('jobs.no_skills') }}
</span>

@endforelse

</div>

</div>

<div class="staff-form-group full">

<label class="staff-form-label">
{{ __('jobs.description_field') }}
</label>

<p style="white-space:pre-line;">
{{ $job->description }}
</p>

</div>

<div class="staff-form-group full">

<label class="staff-form-label">
{{ __('jobs.requirements') }}
</label>

<p style="white-space:pre-line;">
{{ $job->requirements ?: '-' }}
</p>

</div>

</div>

<div class="staff-form-actions">

<a
href="{{ route('admin.jobs.index') }}"
class="staff-btn staff-btn-secondary"
>
{{ __('jobs.cancel') }}
</a>

</div>

</section>

</div>

@endsection