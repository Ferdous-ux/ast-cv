<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;


class AdminActivityNotification extends Notification
{

    use Queueable;



    public function __construct(
        public string $title,
        public array $data = []
    )
    {

    }





    /**
     * Notification channels
     */
    public function via($notifiable): array
    {

        return [
            'database'
        ];

    }







    /**
     * Database notification data
     */
    public function toArray($notifiable): array
    {

        return [

            'title' => $this->title,


            'data' => $this->data,


        ];

    }



}