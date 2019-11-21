<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as ResponseCode;
use App\Repositories\ServiceRepositoryInterface;

class ServiceProviderController extends Controller
{
    /**
     * @var ServiceRepositoryInterface
     */
    private $serviceRepository;

    /**
     * ServiceProviderController constructor.
     * @param ServiceRepositoryInterface $serviceRepository
     */
    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function createService(Request $request)
    {
        dd($request->user());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response = $this->serviceRepository->newRecord(
            $request->input('name'),
            1,
            $request->input('category_id')
        );
    }
}
