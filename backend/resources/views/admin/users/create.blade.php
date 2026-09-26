@extends('admin.layouts.app')


@section('title', __('users.create_title'))


@section('page-title')
{{ __('users.create_title') }}
@endsection


@section('page-description')
{{ __('users.create_description') }}
@endsection





@section('content')


<div class="dashboard">





<section class="dashboard-card staff-form-card">





<div class="dashboard-card__header">


<div>


<h3>

{{ __('users.create_title') }}

</h3>



<p>

{{ __('users.create_description') }}

</p>



</div>


</div>









<form method="POST"
action="{{ route('admin.users.store') }}">


@csrf








<div class="staff-form-grid">







<div class="staff-form-group">


<label class="staff-form-label">

{{ __('users.name') }}

</label>




<input
type="text"
name="name"
class="staff-form-input"
value="{{ old('name') }}"
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
value="{{ old('email') }}"
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
>



@error('password')

<span class="form-error">

{{ $message }}

</span>

@enderror



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('users.password_confirmation') }}

</label>




<input
type="password"
name="password_confirmation"
class="staff-form-input"
>



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('users.role') }}

</label>




<select
name="role"
class="staff-form-select"
>


<option value="">

{{ __('users.select_role') }}

</option>





@foreach($roles as $role)


<option

value="{{ $role->id }}"

{{ old('role') == $role->id ? 'selected' : '' }}

>


{{ $role->name }}


</option>



@endforeach




</select>



</div>









<div class="staff-form-group">


<label class="staff-form-label">

{{ __('users.status') }}

</label>





<select

name="status"

class="staff-form-select"

>




<option value="active">

{{ __('users.active') }}

</option>





<option value="inactive">

{{ __('users.inactive') }}

</option>





</select>



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


{{ __('users.save') }}



</button>







</div>







</form>






</section>







</div>



@endsection