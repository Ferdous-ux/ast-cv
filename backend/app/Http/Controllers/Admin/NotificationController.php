<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class NotificationController extends Controller
{


    /**
     * Display all notifications
     */
    public function index()
    {


        $notifications = auth()
            ->user()
            ->notifications()
            ->latest()
            ->paginate(15);




        return view(
            'admin.notifications.index',
            compact('notifications')
        );


    }









    /**
     * Mark single notification as read
     */
    public function read(
        string $id
    )
    {


        $notification = auth()
            ->user()
            ->notifications()
            ->where('id',$id)
            ->firstOrFail();





        $notification->markAsRead();




        return back();


    }









    /**
     * Mark all notifications as read
     */
    public function readAll()
    {


        auth()
            ->user()
            ->unreadNotifications
            ->markAsRead();





        return back()
            ->with(
                'success',
                __('notifications.mark_all_read')
            );


    }









    /**
     * Delete single notification
     */
    public function destroy(
        string $id
    )
    {


        $notification = auth()
            ->user()
            ->notifications()
            ->where('id',$id)
            ->firstOrFail();





        $notification->delete();





        return back();


    }









    /**
     * Delete all notifications
     */
    public function destroyAll()
    {


        auth()
            ->user()
            ->notifications()
            ->delete();





        return back()
            ->with(
                'success',
                __('notifications.delete_all')
            );


    }



}