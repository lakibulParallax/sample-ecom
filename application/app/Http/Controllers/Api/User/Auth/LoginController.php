<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Traits\GeoCoderTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use ApiResponseTrait, GeoCoderTrait;

    /*Login function*/

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:14|max:14'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            $otp = rand(1000, 9999);

            $user = new User();
            $user->phone = $request->phone;
            $user->otp = $otp;
            $user->is_active = 1;
            $user->otp_expires_at = Carbon::now()->addDay();
            $user->phone_verified_at = Carbon::now();
            $user->save();

            $data['message'] = 'New User Registered';
            $data['otp'] = $otp;
            return $this->successApiResponse($data);
        }

        if($user->is_active == 0){
            $message = "User is not active or Account has been suspended";
            return response()->json(['message' => $message], 400);
        }

        if($user->otp && $user->otp_expires_at <= Carbon::now()){
            if ($user->is_active) {
                $otp = rand(1000, 9999);
                $user->otp = $otp;
                $user->otp_expires_at = Carbon::now()->addDay();
                $user->save();

                $data['message'] = 'OTP send successfully';
                $data['otp'] = $otp;
                return $this->successApiResponse($data);
            } else {
                $token = null;
                $message = "User not active or Account has been suspended";
                return response()->json(['token' => $token, 'message' => $message], 400);
            }

        } else {
            $otp = rand(1000, 9999);
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addDay();
            $user->save();

            $data['message'] = 'OTP send successfully';
            $data['otp'] = $otp;
            return $this->successApiResponse($data);
        }
    }

    public static function login_process_passport($user)
    {
        Auth::loginUsingId($user->id, true);
        return $user->createToken('LaravelAuthApp')->accessToken;
    }

    public function check_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:14|max:14',
            'otp' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user = User::where('phone', $request->phone)->where('is_active', 1)->first();
        if($user){
            $otp_check = User::where('phone', $request->phone)
                ->where('is_active', 1)
                ->where('otp', $request->otp)
                ->where('otp_expires_at', '>=', Carbon::now())
                ->first();

            if($otp_check){
                $token = self::login_process_passport($user);
                $message = "Login Successful";

                $user->otp = null;
                $user->otp_expires_at = null;
                $user->phone_verified_at = Carbon::now();
                $user->save();
            } else {
                $token = null;
                $message = "OTP Expires or Invalid!";
                return response()->json(['token' => $token, 'message' => $message], 400);
            }
            return response()->json(['token' => $token, 'message' => $message]);
        } else {
            $message = "User not active or not found";
            return $this->successApiResponse($message);
        }
    }

    public function resend_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:14|max:14'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user = User::where('phone', $request->phone)->where('is_active', 1)->first();
        if($user){
            $otp = rand(1000, 9999);
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addDay();
            $user->save();

            $data['message'] = 'Resend otp successful';
            $data['otp'] = $otp;
            return $this->successApiResponse($data);
        } else {
            $data['message'] = "User not active or not found";
            return $this->failureApiResponse($data);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nid' => 'required|numeric',
            'phone' => 'required||min:14|max:14']);


        if ($validator->fails()) {
            $response['message'] = $validator->errors()->first();
            return $this->failureApiResponse($response);
        }

        $phoneExist = User::where('phone', $request->phone)->first();
        if ($phoneExist) {
            return response()->json([
                'status' => 422,
                'message' => "Phone Number already exist. Please try another Phone Number."
            ], 422);
        }
        $temporary_token = Str::random(40);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => 1,
            'temporary_token' => $temporary_token,
            'address' => json_encode($request->address)
        ]);

        if($user){
            $data['message'] = 'User Registration Completed';
            return $this->successApiResponse($data);
        } else {
            $data['message'] = 'Error Occurred!';
            return $this->failureApiResponse($data);
        }
    }
    /*Logout*/
    public function logout(Request $request)
    {
        if (Auth::user()) {

            $user = User::where('id', Auth::id())->first();
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            Auth::user()->token()->revoke();
            $response['message'] = "Successfully Logged out";
            return response()->json($response, 200);
        } else {
            $response['message'] = "Token Invalid";
            return response()->json($response, 401);
        }
    }
}
