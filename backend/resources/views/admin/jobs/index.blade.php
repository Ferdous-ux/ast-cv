@extends('admin.layouts.app')

@section('title', __('jobs.title'))

@section('page-title')
{{ __('jobs.title') }}
@endsection

@section('page-description')
{{ __('jobs.management') }}
@endsection

@section('content')

<div class="dashboard staff-page">

<section class="platform-hero">

<div class="platform-hero__content">

<span class="platform-badge">
{{ __('jobs.title') }}
</span>

<h2>
{{ __('jobs.management') }}
</h2>

<p>
{{ __('jobs.description') }}
</p>

</div>

<div class="platform-hero__meta">

<div class="platform-stat">
<span>{{ __('jobs.total_jobs') }}</span>
<strong>{{ $totalJobs }}</strong>
</div>

<div class="platform-stat">
<span>{{ __('jobs.active_jobs') }}</span>
<strong>{{ $activeJobs }}</strong>
</div>

</div>

</section>

<section class="dashboard-card">

<div class="dashboard-card__header">

<div>
<h3>{{ __('jobs.list') }}</h3>
<p>{{ __('jobs.list_description') }}</p>
</div>

<a
href="{{ route('admin.jobs.create') }}"
class="quick-action"
>
<span>+</span>
{{ __('jobs.add') }}
</a>

</div>

<form
method="GET"
action="{{ route('admin.jobs.index') }}"
class="staff-form-grid"
style="margin-bottom:24px;"
>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('jobs.search') }}
</label>

<input
type="text"
name="search"
class="staff-form-input"
value="{{ request('search') }}"
placeholder="{{ __('jobs.search_placeholder') }}"
>

</div>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('jobs.status') }}
</label>

<select
name="status"
class="staff-form-select"
>

<option value="">
{{ __('jobs.all_statuses') }}
</option>

<option
value="draft"
{{ request('status') === 'draft' ? 'selected' : '' }}
>
{{ __('jobs.draft') }}
</option>

<option
value="active"
{{ request('status') === 'active' ? 'selected' : '' }}
>
{{ __('jobs.active') }}
</option>

<option
value="closed"
{{ request('status') === 'closed' ? 'selected' : '' }}
>
{{ __('jobs.closed') }}
</option>

</select>

</div>

<div class="staff-form-actions">

<button
type="submit"
class="staff-btn staff-btn-primary"
>
{{ __('jobs.filter') }}
</button>

</div>

</form>

<div class="table-wrapper staff-table-wrapper">

<table class="admin-table">

<thead>

<tr>
<th>#</th>
<th>{{ __('jobs.title_field') }}</th>
<th>{{ __('jobs.company_name') }}</th>
<th>{{ __('jobs.skills') }}</th>
<th>{{ __('jobs.status') }}</th>
<th>{{ __('jobs.actions') }}</th>
</tr>

</thead>

<tbody>

@forelse($jobs as $job)

<tr>

<td>
{{ $job->id }}
</td>

<td>

<div class="user-cell">

<div class="user-avatar">
{{ mb_substr($job->title, 0, 1) }}
</div>

<span>
{{ $job->title }}
</span>

</div>

</td>

<td>
{{ $job->company_name ?: '-' }}
</td>

<td>

<div class="role-list">

@forelse($job->skills->take(4) as $skill)

<span class="role-badge">
{{ $skill->name }}
</span>

@empty

<span>
{{ __('jobs.no_skills') }}
</span>

@endforelse

@if($job->skills->count() > 4)

<span class="role-badge">
+{{ $job->skills->count() - 4 }}
</span>

@endif

</div>

</td>

<td>

@if($job->status === 'active')

<span class="status-active">
{{ __('jobs.active') }}
</span>

@elseif($job->status === 'closed')

<span class="role-badge">
{{ __('jobs.closed') }}
</span>

@else

<span class="role-badge">
{{ __('jobs.draft') }}
</span>

@endif

</td>

<td>

<div class="table-actions">

<a
href="{{ route('admin.jobs.show', $job->id) }}"
class="action-edit"
>
{{ __('jobs.view') }}
</a>

<a
href="{{ route('admin.jobs.edit', $job->id) }}"
class="action-edit"
>
{{ __('jobs.edit') }}
</a>

<form
method="POST"
action="{{ route('admin.jobs.destroy', $job->id) }}"
onsubmit="return confirm('{{ __('jobs.delete_confirm') }}')"
>

@csrf
@method('DELETE')

<button
type="submit"
class="action-delete"
>
{{ __('jobs.delete') }}
</button>

</form>

</div>

</td>

</tr>

@empty

<tr>

<td
colspan="6"
style="text-align:center;"
>
{{ __('jobs.no_jobs') }}
</td>

</tr>

@endforelse

</tbody>

</table>

</div>

<div class="pagination-wrapper">
{{ $jobs->links() }}
</div>

</section>

</div>

@endsection