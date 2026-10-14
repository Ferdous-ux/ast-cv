@extends('admin.layouts.app')


@section('title', __('permissions.title'))


@section('page-title')
    {{ __('permissions.title') }}
@endsection



@section('page-description')
    {{ __('permissions.description') }}
@endsection






@section('content')


<div class="permissions-page">



<section class="permissions-hero">


    <div class="permissions-hero__icon">
        🔐
    </div>



    <div>

        <h2>
            {{ __('permissions.management') }}
        </h2>


        <p>
            {{ __('permissions.description') }}
        </p>


    </div>




    <div class="permissions-counter">

        <span>
            {{ __('permissions.total') }}
        </span>


        <strong>
            {{ $permissions->total() }}
        </strong>


    </div>


</section>









<section class="permissions-card">


<div class="permissions-card__header">

<h3>
{{ __('permissions.add') }}
</h3>

</div>





<form
method="POST"
action="{{ route('admin.permissions.store') }}"
class="permissions-form"
>

@csrf



<div class="permissions-field">


<label>
{{ __('permissions.name') }}
</label>



<input
type="text"
name="name"
placeholder="{{ __('permissions.name_placeholder') }}"
required
>


</div>





<button
class="permissions-btn"
type="submit"
>

+ {{ __('permissions.add') }}

</button>



</form>



</section>









<section class="permissions-card">





<div class="permissions-card__header">


<div>


<h3>
{{ __('permissions.list') }}
</h3>


<p>
{{ __('permissions.list_description') }}
</p>



</div>


</div>








<div class="permissions-table-wrapper">



<table class="permissions-table">



<thead>


<tr>


<th>
#
</th>



<th>
{{ __('permissions.name') }}
</th>



<th>
{{ __('permissions.guard') }}
</th>



<th>
{{ __('permissions.actions') }}
</th>



</tr>


</thead>







<tbody>



@forelse($permissions as $permission)



<tr>





<td>

{{ $permissions->firstItem() + $loop->index }}

</td>








<td>



<div class="permission-name">



<span class="permission-icon">

🔑

</span>





@php

$key = str_replace('.', '_', $permission->slug);

$nameTranslation = __('permission_names.names.' . $key);

@endphp




@if($nameTranslation != 'permission_names.names.' . $key)

{{ $nameTranslation }}

@else

{{ $permission->name }}

@endif





</div>



</td>









<td>


<span class="permission-badge">


@php

$guard = $permission->guard_name;

@endphp



@if($guard == 'web')

    المستخدمون

@elseif($guard == 'admin')

    الإدارة

@elseif(!empty($guard))

    {{ $guard }}

@else

    غير محدد

@endif



</span>



</td>









<td>



<form

method="POST"

action="{{ route('admin.permissions.destroy',$permission) }}"

>


@csrf

@method('DELETE')






<button

class="permission-delete"

onclick="return confirm('{{ __('permissions.delete_confirm') }}')"

>



{{ __('permissions.delete') }}



</button>



</form>



</td>







</tr>







@empty





<tr>


<td colspan="4" class="empty">


{{ __('permissions.empty') }}


</td>


</tr>





@endforelse





</tbody>





</table>





</div>









<div class="pagination-wrapper">



@if ($permissions->hasPages())


<ul class="pagination">





@if ($permissions->onFirstPage())


<li class="disabled">

<span>
‹
</span>

</li>



@else


<li>

<a href="{{ $permissions->previousPageUrl() }}">
‹
</a>

</li>



@endif









@foreach ($permissions->getUrlRange(1,$permissions->lastPage()) as $page=>$url)



@if($page == $permissions->currentPage())



<li class="active">

<span>

{{ $page }}

</span>


</li>



@else


<li>

<a href="{{ $url }}">

{{ $page }}

</a>


</li>



@endif



@endforeach









@if($permissions->hasMorePages())


<li>

<a href="{{ $permissions->nextPageUrl() }}">

›

</a>

</li>



@else


<li class="disabled">

<span>

›

</span>

</li>



@endif






</ul>



@endif



</div>






</section>







</div>



@endsection