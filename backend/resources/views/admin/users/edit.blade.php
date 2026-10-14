@extends('admin.layouts.app')


@section('title', __('users.edit_user'))


@section('page-title', __('users.edit_user'))


@section('page-description')
{{ __('users.update_description') }}
@endsection





@section('content')


<div class="dashboard">





<section class="dashboard-card staff-form-card">





<div class="dashboard-card__header">


<div>


<h3>

{{ __('users.edit_user') }}

</h3>



<p>

{{ __('users.update_description') }}

</p>



</div>


</div>








<form method="POST"
action="{{ route('admin.users.update',$user->id) }}">


@csrf

@method('PUT')








<div class="staff-form-grid">







<div class="staff-form-group">


<label class="staff-form-label">

{{ __('users.name') }}

</label>




<input
type="text"
name="name"
class="staff-form-input"
value="{{ old('name',$user->name) }}"
>





@error('name')

<span class="form-error">

{{ $message }}

</span>

@enderror



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('users.email') }}

</label>




<input
type="email"
name="email"
class="staff-form-input"
value="{{ old('email',$user->email) }}"
>





@error('email')

<span class="form-error">

{{ $message }}

</span>

@enderror



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('users.password') }}

</label>




<input
type="password"
name="password"
class="staff-form-input"
placeholder="{{ __('users.keep_current_password') }}"
>



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('users.status') }}

</label>





<select
name="status"
class="staff-form-select"
>





<option value="active"
@if($user->status === 'active') selected @endif
>

{{ __('users.active') }}

</option>





<option value="inactive"
@if($user->status === 'inactive') selected @endif
>

{{ __('users.inactive') }}

</option>





</select>



</div>








</div>









<div class="staff-form-group full">



<label class="staff-form-label">

{{ __('users.role') }}

</label>







<div class="roles-box">





@foreach($roles as $role)





<label class="role-option">





<input
type="checkbox"
name="roles[]"
value="{{ $role->id }}"

@if($user->roles->contains('id',$role->id))

checked

@endif

>





<span>

{{ $role->name }}

</span>





</label>





@endforeach






</div>







</div>









<div class="staff-form-actions">





<a href="{{ route('admin.users.index') }}"
class="staff-btn staff-btn-secondary">


{{ __('users.cancel') }}


</a>









<button
type="submit"
class="staff-btn staff-btn-primary"
>


{{ __('users.update') }}



</button>







</div>







</form>






</section>







</div>



@endsection