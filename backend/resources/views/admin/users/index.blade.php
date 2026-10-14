@extends('admin.layouts.app')


@section('title', __('users.title'))


@section('page-title')
{{ __('users.title') }}
@endsection


@section('page-description')
{{ __('users.description') }}
@endsection



@section('content')


<div class="dashboard users-page">





<section class="platform-hero">



<div class="platform-hero__content">



<div class="platform-badge">

{{ __('users.title') }}

</div>





<h2>

{{ __('users.management') }}

</h2>





<p>

{{ __('users.description') }}

</p>



</div>









<div class="platform-meta">



<div class="platform-meta-card">



<span>

{{ __('users.total') }}

</span>



<strong>

{{ $users->total() }}

</strong>



</div>





</div>



</section>









<section class="dashboard-card">





<div class="dashboard-card__header">





<div>



<h3>

{{ __('users.list') }}

</h3>



<p>

{{ __('users.description') }}

</p>



</div>









<a href="{{ route('admin.users.create') }}"
class="quick-action">



<div class="quick-action__icon">

+

</div>





<div>


<strong>

{{ __('users.add') }}

</strong>



<small>

{{ __('users.create_description') }}

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
{{ __('users.user') }}
</th>


<th>
{{ __('users.email') }}
</th>


<th>
{{ __('users.role') }}
</th>


<th>
{{ __('users.status') }}
</th>


<th>
{{ __('users.actions') }}
</th>


</tr>



</thead>









<tbody>





@forelse($users as $user)





<tr>





<td>

{{ $users->firstItem() + $loop->index }}

</td>











<td>



<div class="user-cell">



<div class="user-avatar">


{{ strtoupper(substr($user->name,0,1)) }}


</div>





<span>

{{ $user->name }}

</span>



</div>



</td>













<td>

{{ $user->email }}

</td>












<td>



<div class="role-list">



@forelse($user->roles as $role)



<span class="role-badge">

{{ $role->name }}

</span>



@empty



<span class="role-badge">

{{ __('users.no_role') }}

</span>



@endforelse



</div>



</td>












<td>



@if($user->status === 'active')


<span class="status-active">

{{ __('users.active') }}

</span>


@else


<span class="status-inactive">

{{ __('users.inactive') }}

</span>


@endif



</td>












<td>



<div class="table-actions">





<a href="{{ route('admin.users.edit',$user->id) }}"
class="action-edit">


{{ __('users.edit') }}


</a>









<form
method="POST"
action="{{ route('admin.users.destroy',$user->id) }}"
onsubmit="return confirm('{{ __('users.delete_confirm') }}');"
>


@csrf

@method('DELETE')




<button
type="submit"
class="action-delete"
>


{{ __('users.delete') }}


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


{{ __('users.empty') }}



</td>



</tr>







@endforelse







</tbody>







</table>







</div>









<div class="pagination-wrapper">


{{ $users->links() }}


</div>







</section>







</div>



@endsection