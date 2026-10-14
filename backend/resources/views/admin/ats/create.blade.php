@extends('admin.layouts.app')

@section('title', __('ats.new_analysis'))

@section('page-title')
{{ __('ats.new_analysis') }}
@endsection

@section('page-description')
{{ __('ats.new_analysis_description') }}
@endsection

@section('content')

<div class="dashboard">

<section class="dashboard-card staff-form-card">

<div class="dashboard-card__header">
<div>
<h3>{{ __('ats.new_analysis') }}</h3>
<p>{{ __('ats.new_analysis_description') }}</p>
</div>
</div>

<form method="POST" action="{{ route('admin.ats.store') }}">

@csrf

<div class="staff-form-grid">

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('ats.resume_version') }}
</label>

<select
name="resume_version_id"
class="staff-form-select"
required
>

<option value="">
{{ __('ats.select_resume') }}
</option>

@foreach($resumeVersions as $version)

<option
value="{{ $version->id }}"
{{ old('resume_version_id') == $version->id ? 'selected' : '' }}
>
{{ $version->resume?->title }}
- V{{ $version->version_number }}
@if($version->resume?->user)
- {{ $version->resume->user->name }}
@endif
</option>

@endforeach

</select>

@error('resume_version_id')
<span class="form-error">{{ $message }}</span>
@enderror

</div>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('ats.job') }}
</label>

<select
name="job_posting_id"
class="staff-form-select"
required
>

<option value="">
{{ __('ats.select_job') }}
</option>

@foreach($jobs as $job)

<option
value="{{ $job->id }}"
{{ old('job_posting_id') == $job->id ? 'selected' : '' }}
>
{{ $job->title }}
@if($job->company_name)
- {{ $job->company_name }}
@endif
</option>

@endforeach

</select>

@error('job_posting_id')
<span class="form-error">{{ $message }}</span>
@enderror

</div>

</div>

<div class="staff-form-actions">

<a
href="{{ route('admin.ats.index') }}"
class="staff-btn staff-btn-secondary"
>
{{ __('ats.cancel') }}
</a>

<button
type="submit"
class="staff-btn staff-btn-primary"
>
{{ __('ats.analyze_now') }}
</button>

</div>

</form>

</section>

</div>

@endsection