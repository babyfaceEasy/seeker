<?php


namespace App\Mixins;


use App\Constants\Status;
use Illuminate\Http\Response as ResponseCode;

class ResponseMixins
{

    // TODO look for a way to refactor this code, especially the response array
    public function sendJsonSuccess()
    {
        return function ($data, $message = null, $status_code=ResponseCode::HTTP_OK) {

            $response = ['status' => Status::SUCCESS];
            $response['data'] = $data ?? [];
            $response['message'] = $message ?? '';
            $response['http_message'] = ResponseCode::$statusTexts[$status_code] ?? '';

            return response()->json($response, $status_code);
        };
    }

    public function sendJsonError()
    {
        return function ($data, $message, $status_code=ResponseCode::HTTP_INTERNAL_SERVER_ERROR){
            $response = ['status' => Status::ERROR];
            $response['data'] = $data ?? [];
            $response['message'] = $message ?? '';
            $response['http_message'] = ResponseCode::$statusTexts[$status_code] ?? '';

            return response()->json($response, $status_code);
        };
    }
}