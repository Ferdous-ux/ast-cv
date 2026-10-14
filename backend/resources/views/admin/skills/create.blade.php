@extends('admin.layouts.app')

@section('title', __('admin_skills.create'))

@section('content')
<div class="dashboard">

    <div class="platform-hero">
        <div>
            <span class="role-badge">
                {{ __('admin_skills.badge') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ __('admin_skills.create') }}
            </h1>

            <p>
                {{ __('admin_skills.create_description') }}
            </p>
        </div>
    </div>


    <div
        class="staff-form-card"
        style="margin-top:20px;"
    >
        <form
            method="POST"
            action="{{ route('admin.skills.store') }}"
        >
            @csrf

            @include(
                'admin.skills.partials.form'
            )
        </form>
    </div>

</div>
@endsection