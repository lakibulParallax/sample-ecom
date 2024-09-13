<?php

namespace App\Helper;

use App\Models\LoginHistory\LoginHistory;
use App\Models\LoginHistory\LoginHistoryGarbage;
use App\Models\Timeline;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Helper
{
    public static function status($id)
    {
        if ($id == 1) {
            $x = 'active';
        } elseif ($id == 0) {
            $x = 'in-active';
        }

        return $x;
    }

}
if (!function_exists('handle_history_for_new_login')) {
    function handle_history_for_new_login($user_id, $specific_date = null)
    {
        if ($specific_date === null){
            $specific_date = Carbon::now()->toDateString();
        }
        $exist_history = LoginHistoryGarbage::where('user_id', $user_id)->where('date', $specific_date)->first();
        if ($exist_history) {
            LoginHistory::create([
                'user_id' => $exist_history->user_id,
                'in_time' => $exist_history->in_time,
                'out_time' => $exist_history->out_time,
                'date' => $exist_history->date,
                'device_id' => $exist_history->device_id,
            ]);
        }
    }
}

if (!function_exists('save_timeline')) {
    function save_timeline($student_id, $message, $remarks, $show_student, $created_by)
    {
        // Insert into timeline table
        return Timeline::create([
            'student_id' => $student_id,
            'message' => $message,
            'remarks' => $remarks,
            'show_student' => $show_student,
            'created_by' => $created_by
        ]);
    }
}

if (!function_exists('login_history_garbage')) {
    function login_history_garbage($user_id, $in_time = null, $out_time = null, $date = null, $device_id = null)
    {
        $currentDate = Carbon::now()->toDateString();

        $loginHistory = LoginHistoryGarbage::firstOrNew([
            'user_id' => $user_id,
            'date' => $currentDate
        ]);
        if ($in_time !== null) {
            $loginHistory->in_time = $in_time;
        }
        if ($out_time !== null) {
            $loginHistory->out_time = $out_time;
        }
        if ($device_id !== null) {
            $loginHistory->device_id = $device_id;
        }
        $loginHistory->save();

        return $loginHistory;
    }
}

if (!function_exists('login_history')) {
    function login_history($user_id, $in_time = null, $out_time = null, $date = null, $device_id = null)
    {
        $currentDate = Carbon::now()->toDateString();

        $loginHistory = LoginHistory::firstOrNew([
            'user_id' => $user_id,
            'date' => $currentDate
        ]);
        if ($in_time !== null) {
            $loginHistory->in_time = $in_time;
        }
        if ($out_time !== null) {
            $loginHistory->out_time = $out_time;
        }
        if ($device_id !== null) {
            $loginHistory->device_id = $device_id;
        }
        $loginHistory->save();

        return $loginHistory;
    }
}

