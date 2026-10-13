@extends('admin.layouts.app')

@section('title', __('admin_languages.create'))

@section('content')
<div class="dashboard">

    <div class="platform-hero">
        <div>
            <span class="role-badge">
                {{ __('admin_languages.badge') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ __('admin_languages.create') }}
            </h1>

            <p>
                {{ __('admin_languages.create_description') }}
            </p>
        </div>
    </div>


    <div
        class="staff-form-card"
        style="margin-top:20px;"
    >
        <form
            method="POST"
            action="{{ route('admin.languages.store') }}"
        >
            @csrf

            @include(
                'admin.languages.partials.form'
            )
        </form>
    </div>

</div>
@endsection