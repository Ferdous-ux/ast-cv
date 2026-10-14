<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;


class StaffCreatedNotification extends Notification
{
    use Queueable;


    public function __construct(
        public string $staffName
    )
    {

    }





    public function via($notifiable)
    {
        return [
            'database'
        ];
    }






    public function toDatabase($notifiable)
    {

        return [

            'title' =>
                __('notifications.staff_created_title'),


            'message' =>
                __('notifications.staff_created_message',
                [
                    'name'=>$this->staffName
                ]),


        ];

    }

}