<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\DeliveryType;
use App\Models\RequestType;
use App\Models\Road;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiResponseTrait;

    public function blocks()
    {
        $blocks = Block::get();
        if($blocks) {
            $data['message'] = 'blocks list';
            $data['data'] = $blocks;
            return $this->successApiResponse($data);
        }
        return $this->successApiResponse('not found');
    }

    public function roads()
    {
        $roads = Road::all();
        if($roads) {
            $data['message'] = 'roads list';
            $data['data'] = $roads;
            return $this->successApiResponse($data);
        }
        return $this->successApiResponse('not found');
    }
    public function request_types()
    {
        $request_types = RequestType::all();
        if($request_types) {
            $data['message'] = 'request type list';
            $data['data'] = $request_types;
            return $this->successApiResponse($data);
        }
        return $this->successApiResponse('not found');
    }
    public function delivery_types()
    {
        $request_types = DeliveryType::all();
        if($request_types) {
            $data['message'] = 'delivery type list';
            $data['data'] = $request_types;
            return $this->successApiResponse($data);
        }
        return $this->successApiResponse('not found');
    }
}
