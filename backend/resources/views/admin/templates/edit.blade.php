@extends('admin.layouts.app')

@section('title', __('templates.edit_title'))

@section('content')
<div class="dashboard">
    <div class="platform-hero">
        <div>
            <span class="role-badge">{{ __('templates.badge') }}</span>
            <h1>{{ __('templates.edit_title') }}</h1>
            <p>{{ __('templates.edit_description') }}</p>
        </div>
    </div>

    <div class="staff-form-card" style="margin-top:20px;">
        <form
            method="POST"
            action="{{ route('admin.templates.update', $template) }}"
        >
            @csrf
            @method('PUT')

            @include('admin.templates.partials.form', [
                'template' => $template,
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
                    {{ __('templates.update') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
