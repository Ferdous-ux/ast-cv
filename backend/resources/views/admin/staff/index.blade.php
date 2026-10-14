@extends('admin.layouts.app')


@section('title', __('staff.title'))


@section('page-title', __('staff.title'))


@section('page-description')
{{ __('staff.management') }}
@endsection





@section('content')


<div class="dashboard staff-page">






<section class="platform-hero">



<div class="platform-hero__content">



<div class="platform-badge">

{{ __('staff.title') }}

</div>





<h2>

{{ __('staff.management') }}

</h2>





<p>

{{ __('staff.description') }}

</p>




</div>







<div class="platform-meta">



<div class="platform-meta-card">


<span>

{{ __('staff.total_staff') }}

</span>



<strong>

{{ $staff->total() }}

</strong>


</div>






<div class="platform-meta-card">


<span>

{{ __('staff.status') }}

</span>



<strong>

{{ __('staff.active') }}

</strong>


</div>




</div>



</section>









<section class="dashboard-card">






<div class="dashboard-card__header">





<div>


<h3>

{{ __('staff.list') }}

</h3>



<p>

{{ __('staff.list_description') }}

</p>



</div>









<a href="{{ route('admin.staff.create') }}"
class="quick-action">


<div class="quick-action__icon">

+

</div>




<div>


<strong>

{{ __('staff.add_staff') }}

</strong>



<small>

{{ __('staff.create_staff') }}

</small>


</div>



</a>




</div>











<div class="table-wrapper staff-table-wrapper">





<table class="admin-table">





<thead>


<tr>


<th>
#
</th>


<th>
{{ __('staff.name') }}
</th>


<th>
{{ __('staff.email') }}
</th>


<th>
{{ __('staff.roles') }}
</th>


<th>
{{ __('staff.status') }}
</th>


<th>
{{ __('staff.actions') }}
</th>


</tr>



</thead>









<tbody>





@forelse($staff as $member)





<tr>





<td>

{{ $loop->iteration }}

</td>









<td>


<div class="user-cell">



<div class="user-avatar">

{{ strtoupper(substr($member->name,0,1)) }}

</div>




<span>

{{ $member->name }}

</span>



</div>



</td>











<td>

{{ $member->email }}

</td>









<td>



<div class="role-list">



@forelse($member->roles as $role)



<span class="role-badge">

{{ $role->name }}

</span>



@empty



<span class="role-badge">

{{ __('staff.no_roles') }}

</span>



@endforelse



</div>



</td>











<td>


@if($member->status === 'active')


<span class="status-active">

{{ __('staff.active') }}

</span>


@elseif($member->status === 'inactive')


<span class="status-inactive">

{{ __('staff.inactive') }}

</span>


@else


<span class="status-suspended">

{{ __('staff.suspended') }}

</span>


@endif



</td>











<td>




<div class="table-actions">







<a
href="{{ route('admin.staff.edit',$member->id) }}"
class="action-edit"
>

{{ __('staff.edit') }}

</a>










<form
method="POST"
action="{{ route('admin.staff.destroy',$member->id) }}"
onsubmit="return confirm('{{ __('staff.delete_confirm') }}');"
>


@csrf

@method('DELETE')



<button
type="submit"
class="action-delete"
>


{{ __('staff.delete') }}


</button>


</form>







</div>




</td>





</tr>









@empty







<tr>



<td colspan="6"
style="text-align:center;padding:40px;"
>


{{ __('staff.no_staff') }}



</td>



</tr>







@endforelse








</tbody>







</table>







</div>












<div class="pagination-wrapper">


{{ $staff->links() }}


</div>









</section>







</div>



@endsection