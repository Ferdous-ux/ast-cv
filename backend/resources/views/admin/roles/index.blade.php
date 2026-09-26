@extends('admin.layouts.app')


@section('title', __('roles.title'))


@section('page-title', __('roles.management'))


@section('page-description')

{{ __('roles.description') }}

@endsection





@section('content')


<div class="dashboard">



<section class="dashboard-card">



<div class="dashboard-card__header">


<div>

<h3>
{{ __('roles.list') }}
</h3>


<p>
{{ __('roles.list_description') }}
</p>


</div>


</div>







<div class="table-wrapper">


<table class="admin-table">


<thead>

<tr>

<th>
#
</th>


<th>
{{ __('roles.name') }}
</th>


<th>
{{ __('roles.users') }}
</th>


<th>
{{ __('roles.permissions') }}
</th>


<th>
{{ __('roles.actions') }}
</th>


</tr>

</thead>







<tbody>



@forelse($roles as $role)



<tr>


<td>
{{ $loop->iteration }}
</td>



<td>

<strong>
{{ $role->name }}
</strong>

</td>




<td>

{{ $role->users_count }}

</td>





<td>

{{ $role->permissions_count }}

</td>





<td>


<a
href="{{ route('admin.roles.edit',$role) }}"
class="btn btn-primary"
>

{{ __('roles.manage') }}

</a>


</td>




</tr>



@empty


<tr>

<td colspan="5"
style="text-align:center;padding:40px"
>

{{ __('roles.empty') }}

</td>

</tr>


@endforelse



</tbody>



</table>



</div>







<div class="pagination-wrapper">

{{ $roles->links() }}

</div>




</section>



</div>



@endsection