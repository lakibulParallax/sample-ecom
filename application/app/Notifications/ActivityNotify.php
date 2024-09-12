<?php

namespace App\Notifications;

use Benwilkins\FCM\FcmMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;


class ActivityNotify extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payload)
    {

        $this->payload = $payload;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }



    public function toFcm($notifiable)
    {
        try {
            $message = new FcmMessage();
            $message->content([
                'title' => @$this->payload->push_title,
                'body' => @$this->payload->push_body ? strip_tags($this->payload->push_body) : '',
                'image' => @$this->payload->push_image ?? '',
                'sound' => '', // Optional
                'icon' => '', // Optional
                'click_action' => @$this->payload->push_click_action ?? '',// Optional
            ])->data([
                'extra_data' => @$this->payload->extra_data ?? '' // Optional
            ])->priority(FcmMessage::PRIORITY_HIGH); // Optional - Default is 'normal'.
            return $message;

        } catch (\Exception $exception) {
            return 200;
        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */


}
