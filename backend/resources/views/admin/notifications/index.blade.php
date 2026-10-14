@extends('admin.layouts.app')


@section('title', __('notifications.title'))


@section('page-title', __('notifications.title'))


@section('page-description')
{{ __('notifications.view_all') }}
@endsection





@section('content')


<div class="dashboard">



<section class="dashboard-card">



<div class="dashboard-card__header">


<div>


<h3>

{{ __('notifications.title') }}

</h3>


<p>

{{ __('notifications.view_all') }}

</p>


</div>



<form
method="POST"
action="{{ route('admin.notifications.destroy-all') }}"
>


@csrf

@method('DELETE')


<button
class="staff-btn staff-btn-secondary"
type="submit"
>

{{ __('notifications.delete_all') }}

</button>


</form>



</div>









<div class="notifications-page">





@forelse($notifications as $notification)



<div class="admin-notification-item
{{ $notification->read_at ? '' : 'unread' }}"
>




<div>


<strong>

{{ __(
    $notification->data['title'] ?? ''
) }}

</strong>





<p>


@if(isset($notification->data['data']['message']))


{{ str_replace(
    ':name',
    $notification->data['data']['name'] ?? '',
    __($notification->data['data']['message'])
) }}



@endif


</p>





<small>

{{ $notification->created_at->diffForHumans() }}

</small>


</div>







<div class="notification-actions">





@if(!$notification->read_at)


<form
method="POST"
action="{{ route(
    'admin.notifications.read',
    $notification->id
) }}"
>


@csrf


<button
type="submit"
class="staff-btn staff-btn-secondary"
>

{{ __('notifications.mark_all_read') }}

</button>


</form>


@endif







<form
method="POST"
action="{{ route(
    'admin.notifications.destroy',
    $notification->id
) }}"
>


@csrf

@method('DELETE')


<button
type="submit"
class="action-delete"
>

×

</button>


</form>





</div>





</div>





@empty



<div class="admin-notification-empty">

{{ __('notifications.empty') }}

</div>



@endforelse





</div>







<div class="pagination-wrapper">


{{ $notifications->links() }}


</div>






</section>



</div>



@endsection