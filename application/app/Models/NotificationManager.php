<?php

namespace App\Models;

use App\Jobs\NotifyUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationManager extends Model
{

    use HasFactory;

    public static function SENDALL($payload){


        return NotifyUser::dispatch($payload)->delay(2);
        NotifyUser::dispatch($payload)->delay(10);
        /*END SEND MESSAGE EMAIL AND NOTIFICATIONS*/

    }
}
