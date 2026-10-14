@extends('admin.layouts.app')

@section('title', __('admin_languages.edit'))

@section('content')
<div class="dashboard">

    <div class="platform-hero">
        <div>
            <span class="role-badge">
                {{ __('admin_languages.badge') }}
            </span>

            <h1 style="margin-top:14px;">
                {{ __('admin_languages.edit') }}
            </h1>

            <p>
                {{ $language->name }}
            </p>
        </div>
    </div>


    <div
        class="staff-form-grid"
        style="margin-top:20px;"
    >
        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_languages.writing_usage') }}
            </div>

            <div
                style="
                    font-size:28px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $language->resumes_count }}
            </div>
        </div>

        <div class="dashboard-card">
            <div class="staff-form-label">
                {{ __('admin_languages.spoken_usage') }}
            </div>

            <div
                style="
                    font-size:28px;
                    font-weight:800;
                    margin-top:8px;
                "
            >
                {{ $language->resume_versions_count }}
            </div>
        </div>
    </div>


    <div
        class="staff-form-card"
        style="margin-top:20px;"
    >
        <form
            method="POST"
            action="{{ route(
                'admin.languages.update',
                $language
            ) }}"
        >
            @csrf
            @method('PUT')

            @include(
                'admin.languages.partials.form'
            )
        </form>
    </div>

</div>
@endsection