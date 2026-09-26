<!DOCTYPE html>
<html
    lang="{{ app()->getLocale() }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', __('admin.dashboard'))
        — AST-CV Admin
    </title>

    @vite([
        'resources/css/admin/app.css',
        'resources/js/admin/app.js',
    ])

    @stack('styles')

</head>

<body>

    <div class="admin-shell">

        @include('admin.layouts.sidebar')

        <div class="admin-main">

            @include('admin.layouts.topbar')

            <main class="admin-content">

                <div class="admin-content__container">

                    @yield('content')

                </div>

            </main>

        </div>

    </div>

    @stack('scripts')

</body>
</html>