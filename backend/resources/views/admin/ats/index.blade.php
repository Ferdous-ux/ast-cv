@extends('admin.layouts.app')

@section('title', __('ats.title'))

@section('page-title')
{{ __('ats.title') }}
@endsection

@section('page-description')
{{ __('ats.management') }}
@endsection

@section('content')

<div class="dashboard staff-page">

<section class="platform-hero">

<div class="platform-hero__content">

<span class="platform-badge">
{{ __('ats.title') }}
</span>

<h2>{{ __('ats.management') }}</h2>

<p>{{ __('ats.description') }}</p>

</div>

<div class="platform-hero__meta">

<div class="platform-stat">
<span>{{ __('ats.total_analyses') }}</span>
<strong>{{ $totalAnalyses }}</strong>
</div>

<div class="platform-stat">
<span>{{ __('ats.average_score') }}</span>
<strong>{{ $averageScore }}%</strong>
</div>

<div class="platform-stat">
<span>{{ __('ats.strong_matches') }}</span>
<strong>{{ $strongMatches }}</strong>
</div>

</div>

</section>

<section class="dashboard-card">

<div class="dashboard-card__header">

<div>
<h3>{{ __('ats.analysis_results') }}</h3>
<p>{{ __('ats.description') }}</p>
</div>

<a
href="{{ route('admin.ats.create') }}"
class="quick-action"
>
<span>+</span>
{{ __('ats.new_analysis') }}
</a>

</div>

<form
method="GET"
action="{{ route('admin.ats.index') }}"
class="staff-form-grid"
style="margin-bottom:24px;"
>

<div class="staff-form-group">

<label class="staff-form-label">
{{ __('ats.search') }}
</label>

<input
type="text"
name="search"
class="staff-form-input"
value="{{ request('search') }}"
placeholder="{{ __('ats.search_placeholder') }}"
>

</div>

<div class="staff-form-actions">

<button
type="submit"
class="staff-btn staff-btn-primary"
>
{{ __('ats.filter') }}
</button>

</div>

</form>

<div class="table-wrapper staff-table-wrapper">

<table class="admin-table">

<thead>

<tr>
<th>#</th>
<th>{{ __('ats.resume') }}</th>
<th>{{ __('ats.job') }}</th>
<th>{{ __('ats.score') }}</th>
<th>{{ __('ats.analyzed_at') }}</th>
<th>{{ __('ats.actions') }}</th>
</tr>

</thead>

<tbody>

@forelse($analyses as $analysis)

<tr>

<td>{{ $analysis->id }}</td>

<td>
<div class="user-cell">

<div class="user-avatar">
{{ mb_substr($analysis->resumeVersion?->resume?->title ?? 'R', 0, 1) }}
</div>

<span>
{{ $analysis->resumeVersion?->resume?->title ?? '-' }}
</span>

</div>
</td>

<td>
{{ $analysis->job?->title ?? '-' }}
</td>

<td>

<span class="role-badge">
{{ number_format($analysis->overall_score, 1) }}%
</span>

</td>

<td>
{{ $analysis->analyzed_at?->format('Y-m-d H:i') ?? '-' }}
</td>

<td>

<div class="table-actions">

<a
href="{{ route('admin.ats.show', $analysis) }}"
class="action-edit"
>
{{ __('ats.view') }}
</a>

<form
method="POST"
action="{{ route('admin.ats.destroy', $analysis) }}"
onsubmit="return confirm('{{ __('ats.delete_confirm') }}')"
>

@csrf
@method('DELETE')

<button
type="submit"
class="action-delete"
>
{{ __('ats.delete') }}
</button>

</form>

</div>

</td>

</tr>

@empty

<tr>
<td colspan="6" style="text-align:center;">
{{ __('ats.no_analyses') }}
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

<div class="pagination-wrapper">
{{ $analyses->links() }}
</div>

</section>

</div>

@endsection