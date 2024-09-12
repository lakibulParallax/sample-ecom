<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    public $successStatus = 200;

    public $failureStatus = 500;

    public $validationfailureStatus = 400;

    public $notAllowedStatus = 401;

    public $notfound = 404;

    public function successApiResponse($response): JsonResponse
    {
        $response = ['status' => 200] + $response;
        return response()->json($response, $this->successStatus);
    }

    public function failureApiResponse($response): JsonResponse
    {
        $response = ['status' => 400] + $response;
        return response()->json($response, $this->validationfailureStatus);
    }

    public function notFoundApiResponse($response): JsonResponse
    {
        $response = ['status' => 404] + $response;
        return response()->json($response, $this->notfound);
    }

    public function dataNotFoundApiResponse($response): JsonResponse
    {
        $response = ['status' => 404] + $response;
        return response()->json($response, $this->notfound);
    }

    public function validationfailureApiResponse($response): JsonResponse
    {
        return response()->json($response, $this->validationfailureStatus);
    }

    public function notAllowedApiResponse($response): JsonResponse
    {
        return response()->json($response, $this->notAllowedStatus);
    }

    public function setFieldValues($fields, $values, $setNULL = array())
    {
        $data = [];
        foreach ($fields as $field) {
            if (isset($values[$field])) {
                if (!empty($values[$field])) {
                    $data[$field] = $values[$field];
                } else if (in_array($field, $setNULL)) {
                    $data[$field] = NULL;
                }
            }
        }
        return $data;
    }
}
