@extends('admin.layouts.app')

@section('title', __('templates.create_title'))

@section('content')
<div class="dashboard">
    <div class="platform-hero">
        <div>
            <span class="role-badge">{{ __('templates.badge') }}</span>
            <h1>{{ __('templates.create_title') }}</h1>
            <p>{{ __('templates.create_description') }}</p>
        </div>
    </div>

    <div class="staff-form-card" style="margin-top:20px;">
        <form
            method="POST"
            action="{{ route('admin.templates.store') }}"
        >
            @csrf

            @include('admin.templates.partials.form', [
                'template' => null,
            ])

            <div class="staff-form-actions">
                <a
                    href="{{ route('admin.templates.index') }}"
                    class="staff-btn staff-btn-secondary"
                >
                    {{ __('templates.cancel') }}
                </a>

                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    {{ __('templates.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
