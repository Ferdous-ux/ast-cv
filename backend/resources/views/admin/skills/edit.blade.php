@extends('admin.layouts.app')

@section('title', __('admin_skills.edit'))

@section('content')
<div class="dashboard">

    <div class="platform-hero">
        <div>
            <span class="role-badge">
                {{ __('admin_skills.badge') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ __('admin_skills.edit') }}
            </h1>

            <p>
                {{ $skill->name }}
            </p>
        </div>
    </div>


    <div
        class="staff-form-card"
        style="margin-top:20px;"
    >
        <form
            method="POST"
            action="{{ route(
                'admin.skills.update',
                $skill
            ) }}"
        >
            @csrf
            @method('PUT')

            @include(
                'admin.skills.partials.form'
            )
        </form>
    </div>

</div>
@endsection