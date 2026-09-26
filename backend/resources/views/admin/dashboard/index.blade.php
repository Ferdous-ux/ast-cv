@extends('admin.layouts.app')


@section('title', __('admin.dashboard'))


@section('page-title', __('admin.dashboard'))


@section('page-description', __('admin.overview'))




@section('content')


<div class="dashboard">





<section class="platform-hero">


<div class="platform-hero__content">



<div class="platform-badge">

{{ __('admin.platform') }}

</div>





<h2>

{{ __('admin.welcome_back') }}

</h2>




<p>

{{ __('admin.platform_description') }}

</p>



</div>






<div class="platform-meta">



<div class="platform-meta-card">


<span>

{{ __('admin.platform_status') }}

</span>



<strong>

{{ __('admin.operational') }}

</strong>


</div>






<div class="platform-meta-card">


<span>

{{ __('admin.last_sync') }}

</span>



<strong>

{{ now()->format('H:i') }}

</strong>


</div>



</div>



</section>









<section class="dashboard__stats">



@foreach($stats as $stat)



<article class="stat-card">



<div class="stat-card__header">



<span class="stat-card__icon">

{{ $stat['icon'] }}

</span>





<span class="stat-card__label">

{{ __('admin.' . strtolower($stat['title'])) }}

</span>



</div>







<strong class="stat-card__value">

{{ number_format($stat['value']) }}

</strong>







<div class="stat-card__footer">



<span class="stat-card__trend">


@if($stat['growth'] === 'active')

{{ __('admin.active') }}

@else

{{ $stat['growth'] }}

@endif


</span>





<span class="stat-card__caption">

{{ __('admin.compared_previous_period') }}

</span>



</div>



</article>



@endforeach



</section>









<section class="dashboard__grid">







<div class="dashboard-card">





<div class="dashboard-card__header">



<div>


<h3>

{{ __('admin.platform_activity') }}

</h3>



<p>

{{ __('admin.activity_description') }}

</p>


</div>






<div class="dashboard-status">


<span></span>


{{ __('admin.live') }}


</div>





</div>







<div class="dashboard-chart">


<canvas id="platformActivityChart"></canvas>


</div>





</div>









<div class="dashboard-card">





<div class="dashboard-card__header">


<h3>

{{ __('admin.performance_overview') }}

</h3>



</div>







<div class="ats-score">



<strong>

{{ $performance['ats'] }}%

</strong>




<span>

{{ __('admin.ats_compatibility') }}

</span>



</div>









<div class="metric-list">



<div class="metric-item">



<div class="metric-item__head">


<span>

{{ __('admin.skills_matching') }}

</span>



<strong>

{{ $performance['skills'] }}%

</strong>



</div>





<div class="metric-bar">


<span style="width:{{ $performance['skills'] }}%"></span>


</div>




</div>







<div class="metric-item">



<div class="metric-item__head">


<span>

{{ __('admin.experience') }}

</span>



<strong>

{{ $performance['experience'] }}%

</strong>



</div>





<div class="metric-bar">


<span style="width:{{ $performance['experience'] }}%"></span>


</div>




</div>





</div>





</div>







</section>









<section class="dashboard-bottom">







<div class="dashboard-card">





<h3>

{{ __('admin.recent_activity') }}

</h3>








<div class="activity-timeline">





@forelse($activities as $activity)





<div class="activity-item">





<div class="activity-icon">


@if($activity['action'] === 'created')

+

@elseif($activity['action'] === 'updated')

✎

@elseif($activity['action'] === 'deleted')

×

@else

•

@endif


</div>







<div class="activity-details">



<strong>

{{ $activity['user'] }}

</strong>






<p>

{{ $activity['title'] }}

</p>






<span>

{{ ucfirst($activity['module']) }}

&nbsp; • &nbsp;

{{ $activity['time'] }}

</span>




</div>






</div>





@empty





<div class="activity-empty">


{{ __('admin.no_activity') }}


</div>





@endforelse





</div>






</div>












<div class="dashboard-card">





<h3>

{{ __('admin.quick_actions') }}

</h3>







<div class="quick-actions">







<a
href="{{ route('admin.staff.create') }}"
class="quick-action">





<div class="quick-action__icon">

+

</div>







<div>



<strong>

{{ __('admin.add_staff') }}

</strong>





<small>

{{ __('admin.create_admin_account') }}

</small>




</div>





</a>








<a
href="#"
class="quick-action">





<div class="quick-action__icon">

+

</div>







<div>



<strong>

{{ __('admin.add_template') }}

</strong>





<small>

{{ __('admin.create_cv_template') }}

</small>




</div>





</a>








<a
href="#"
class="quick-action">





<div class="quick-action__icon">

+

</div>







<div>



<strong>

{{ __('admin.manage_skills') }}

</strong>





<small>

{{ __('admin.update_ats_database') }}

</small>




</div>





</a>







</div>






</div>







</section>







</div>







<script>


window.dashboardChart = @json($chart);


</script>



@endsection