<?php

namespace App\Http\Controllers\Api\User;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\FileManager;
use App\Models\Road;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Traits\GeoCoderTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mockery\Exception;
use Carbon\Carbon;

class UserController extends Controller
{
    use ApiResponseTrait, GeoCoderTrait;

    public function details(Request $request)
    {
        $data = User::where('id', Auth::id())->first();
        $response['message'] = "Info fetched successfully";
        $response['user_info'] = $data;
        return $this->successApiResponse($response);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = User::find(Auth::id());
        $user->name = $request->input('name') ?? $user->name;
        $user->nid = $request->input('nid') ?? $user->nid;
        $user->address = $request->input('address') ?? $user->address;
        if($user->email != $request->input('email')) {
            $user->email = $request->input('email') ?? $user->email;
        }
        if ($request->input('device_token')) {
            $user->device_token = $request->input('device_token');
        }
        $user->save();

        $insertedId = $user->id;
        $user_details = User::find($user->id);
        $data['message'] = "User Info Updated Successfully";
        $data['data'] = $user_details;

        $blockModel = new Block();
        $roadModel = new Road();
        $modifiedAddresses = [];
        if(!empty($user_details->address)) {
            foreach ($user_details->address as $address) {
                $blockData = $blockModel->find($address['block_id']);
                $roadData = $roadModel->find($address['road_id']);

                $modifiedAddress = $address;
                $modifiedAddress['block'] = $blockData;
                $modifiedAddress['road'] = $roadData;

                $modifiedAddresses[] = $modifiedAddress;
            }
            $data['data']['address'] = $modifiedAddresses;
        }

        return $this->successApiResponse($data);
    }

    public function showProfile(Request $request): JsonResponse
    {
        $user = User::where('id', Auth::id())->first();
        if($user) {
            $data['message'] = 'user information';
            $data['data'] = $user;

            if(!empty($user->address)){
                $addresses = $user->address;
                foreach ($addresses as &$address) {
                    $blockData = Block::find($address['block_id']);
                    $address['block_id'] = (int) $address['block_id'];
                    $address['block'] = $blockData;

                    $roadData = Road::find($address['road_id']);
                    $address['road_id'] = (int) $address['road_id'];
                    $address['road'] = $roadData;
                }
                $data['data']['address'] = $addresses;
            }

        }
        return $this->successApiResponse($data);
    }

    public function deleteDeviceToken(Request $request)
    {
        $data = array();
        $user = User::find(Auth::id());
        $user->device_token = null;
        $user->save();
        $data['message'] = "success";
        return $this->successApiResponse($data);
    }
}
