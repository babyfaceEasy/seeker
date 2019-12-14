<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use Illuminate\Http\Request;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as ResponseCode;
use App\Repositories\ServiceRepositoryInterface;

class CustomerController extends Controller
{

    /**
     * @var ServiceRepositoryInterface
     */
    private $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function exploreServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_param' => 'required|string'
        ]);

        if ($validator->fails()){
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }
        $services = $this->serviceRepository->search($request->input('search_param'));
        if (is_string($services) && $services == Status::ERROR){
            // log error
            $services = [];
        }
        return response()->sendJsonSuccess(['data' => $services]);
    }
}
