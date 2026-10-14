@extends('admin.layouts.app')


@section('title', __('roles.edit'))



@section('page-title')

    {{ __('roles.edit') }}

@endsection



@section('page-description')

    {{ __('roles.edit_description') ?? $role->name }}

@endsection





@push('styles')

@vite([
    'resources/css/admin/pages/role/edit.css'
])

@endpush







@section('content')



<div class="dashboard role-permissions-page">





<section class="dashboard-card">






<div class="role-header-card">



<div>


<h2>

{{ $role->name }}

</h2>



<p>

{{ $role->description }}

</p>


</div>







<div class="role-total">


<strong>

{{ $role->permissions->count() }}

</strong>



<span>

{{ __('roles.permissions') }}

</span>


</div>



</div>









<form

method="POST"

action="{{ route('admin.roles.update',$role) }}"

>


@csrf

@method('PUT')









@foreach($permissions as $group => $items)





<div class="permission-panel">






<div class="permission-panel-header">



<div>


<h4>

{{ __('permission_names.groups.' . $group) }}

</h4>



<span class="permission-count">

{{ count($items) }}

{{ __('roles.permissions') }}

</span>


</div>





<button

type="button"

class="permission-select-all"

data-group="{{ $group }}"

>

{{ __('roles.select_all') }}

</button>



</div>









<div class="permission-grid">





@foreach($items as $permission)



@php

$key = str_replace('.', '_', $permission->slug);

@endphp






<label class="permission-item">






<input

type="checkbox"

name="permissions[]"

value="{{ $permission->id }}"

class="permission-checkbox-{{ $group }}"

@checked(
$role->permissions->contains('id',$permission->id)
)

>









<span class="permission-toggle"></span>











<div class="permission-text">



<strong>

{{ __('permission_names.names.' . $key) }}

</strong>



<small>

{{ __('permission_names.descriptions.' . $key) }}

</small>





</div>






</label>







@endforeach






</div>









</div>







@endforeach













<div class="permission-save">



<button

type="submit"

class="btn btn-primary"

>



{{ __('roles.save_permissions') }}



</button>



</div>









</form>






</section>






</div>







@endsection












@push('scripts')


<script>


document
.querySelectorAll('.permission-select-all')
.forEach(button => {



button.addEventListener('click', function(){



let group = this.dataset.group;



let checkboxes = document.querySelectorAll(

'.permission-checkbox-' + group

);




let allChecked = [...checkboxes]
.every(item => item.checked);




checkboxes.forEach(item => {


item.checked = !allChecked;



});



});



});



</script>


@endpush