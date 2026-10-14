@php
    $type = session('alert_type', 'success');

    $classes = [

        'success' => 'alert-success',

        'error' => 'alert-error',

        'warning' => 'alert-warning',

        'info' => 'alert-info',

    ];

@endphp



@if(session('success') || session('error') || session('warning') || session('info'))


<div class="admin-alert {{ $classes[$type] ?? $classes['success'] }}">


    <span class="admin-alert__icon">

        @if($type === 'success')
            ✓
        @elseif($type === 'error')
            ×
        @elseif($type === 'warning')
            !
        @else
            i
        @endif

    </span>



    <span class="admin-alert__message">


        {{ 
            session('success')
            ??
            session('error')
            ??
            session('warning')
            ??
            session('info')
        }}


    </span>


</div>


@endif