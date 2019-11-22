<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use Illuminate\Http\Request;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as ResponseCode;
use App\Repositories\ServiceRepositoryInterface;

class ServiceController extends Controller
{
    private $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index()
    {
        $services = $this->serviceRepository->all();
        return response()->sendJsonSuccess($services);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric'
        ]);

        if ($validator->fails()){
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response = $this->serviceRepository->newRecord(
            $request->input('name'),
            $request->input('user_id'),
            $request->input('category_id')
        );

        if ($response === Status::SUCCESS){
            return response()->sendJsonSuccess([], sprintf( ResponseMessage::RESOURCE_CREATED, 'Service'), ResponseCode::HTTP_CREATED);
        }

        return response()->sendJsonError([], ResponseMessage::ERROR_OCCURRED, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);

    }
}
