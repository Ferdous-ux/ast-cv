@extends('admin.layouts.app')


@section('title', __('staff.edit_staff'))


@section('page-title', __('staff.edit_staff'))


@section('page-description')
{{ __('staff.update_description') }}
@endsection





@section('content')


<div class="dashboard">





<section class="dashboard-card staff-form-card">





<div class="dashboard-card__header">


<div>


<h3>

{{ __('staff.edit_staff') }}

</h3>



<p>

{{ __('staff.update_description') }}

</p>



</div>


</div>









<form method="POST"
action="{{ route('admin.staff.update',$staff->id) }}">


@csrf

@method('PUT')









<div class="staff-form-grid">







<div class="staff-form-group">


<label class="staff-form-label">

{{ __('staff.name') }}

</label>




<input
type="text"
name="name"
class="staff-form-input"
value="{{ old('name',$staff->name) }}"
>





@error('name')

<span class="form-error">

{{ $message }}

</span>

@enderror



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('staff.email') }}

</label>




<input
type="email"
name="email"
class="staff-form-input"
value="{{ old('email',$staff->email) }}"
>





@error('email')

<span class="form-error">

{{ $message }}

</span>

@enderror



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('staff.password') }}

</label>




<input
type="password"
name="password"
class="staff-form-input"
placeholder="{{ __('staff.keep_current_password') }}"
>





@error('password')

<span class="form-error">

{{ $message }}

</span>

@enderror



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('staff.status') }}

</label>





<select
name="status"
class="staff-form-select"
>





<option value="active"
@if($staff->status === 'active') selected @endif
>

{{ __('staff.active') }}

</option>






<option value="inactive"
@if($staff->status === 'inactive') selected @endif
>

{{ __('staff.inactive') }}

</option>







<option value="suspended"
@if($staff->status === 'suspended') selected @endif
>

{{ __('staff.suspended') }}

</option>





</select>



</div>






</div>













<div class="staff-form-group full">



<label class="staff-form-label">

{{ __('staff.roles') }}

</label>







<div class="roles-box">





@foreach($roles as $role)





<label class="role-option">





<input
type="checkbox"
name="roles[]"
value="{{ $role->id }}"

@if(
$staff->roles->contains('id',$role->id)
)

checked

@endif

>





<span>

{{ $role->name }}

</span>





</label>







@endforeach






</div>









@error('roles')

<span class="form-error">

{{ $message }}

</span>

@enderror






</div>













<div class="staff-form-actions">





<a href="{{ route('admin.staff.index') }}"
class="staff-btn staff-btn-secondary">


{{ __('staff.cancel') }}


</a>









<button
type="submit"
class="staff-btn staff-btn-primary"
>


{{ __('staff.update') }}



</button>






</div>








</form>







</section>






</div>



@endsection