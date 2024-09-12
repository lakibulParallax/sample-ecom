<?php

namespace App\Jobs;

use App\CentralLogics\Helpers;
use App\Mail\MatchRequested;
use App\Mail\MessageTemplate;
use App\Mail\VendorCreate;
use App\Models\Notification;
use App\Models\SmsSender;
use App\Models\User;
use App\Notifications\ActivityNotify;
use App\Notifications\DailyActivityNotify;
use GPBMetadata\Google\Api\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use mysql_xdevapi\Exception;
use stdClass;

class NotifyUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->payload;
        $payload = (object)$payload;

        /*FOR SMS*/
//        if (@$payload->sms == "1" && config('app.sms_enable') === true) {
//            $sms = new SmsSender();
//            $sms->sms_subject = @$payload->sms_subject;
//            $sms->sms_body = @$payload->sms_body;
//            $sms->sms_number = @$payload->sms_number;
//            $sms->send();
//        }

        /*TO DO FOR PUSH NOTIFICATION*/
        if (@$payload->push == "1") {
            $notification = new Notification();
            $notification->user_id = @$payload->user->id;
            $notification->user_type = @$payload->user_type;
            $notification->type = @$payload->push_type;
            $notification->sender_id = @$payload->sender_id;
            $notification->sender_type = @$payload->sender_type;
            $notification->title = @$payload->push_title;
            $notification->body = @$payload->push_body;
            $notification->data = json_encode(@$payload->extra_data);
            $notification->save();

            try {
                @$payload->user->notify((new ActivityNotify($payload)));
            } catch (Exception $exception){
                \Illuminate\Support\Facades\Log::info(["message"=> $exception->getMessage()]);
            }
            //Helpers::message_log($payload->user->id, $payload->push_body, $payload->type ?? null, $payload->push_title, 'Send successfully', $payload->message_template_id ?? null);
        }

        /*SENDING EMAILS*/
//        if (@$payload->email == "1" && @$payload->user->email) {
//            /*customer Email*/
//            $mail_object = new stdClass();
//            $mail_object->name = @$payload->user->name;
//            $mail_object->push_title = @$payload->push_title;
//            $mail_object->push_body = @$payload->push_body;
//            $mail_object->push_type = @$payload->push_type;
//            $mail_object->push_image = @$payload->push_image;
//            if (@$payload->email_type == 1 && @$payload->user->email) {
//                try {
//                    Mail::to(@$payload->user->email)->send(new MessageTemplate($mail_object));
//                    Helpers::message_log($payload->user->id, $payload->push_body, $payload->type, $payload->push_title, 'Send successfully', $payload->message_template_id ?? null, $payload->sender_type);
//                } catch (\Exception $exception) {
//                    $errorMessage = $exception->getMessage();
//                    Helpers::message_log($payload->user->id, $payload->push_body, $payload->type, $payload->push_title, 'Not send - Error: '.$errorMessage, $payload->message_template_id ?? null, $payload->sender_type);
//                }
//
//            }
//
//        }

    }
}
