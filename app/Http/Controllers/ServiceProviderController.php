<?php

namespace App\Http\Controllers;

use App\Constants\Status;
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
        //dd($request->user());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category_id' => 'required|numeric',
            'picture' => 'required',
            'picture_two' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $service = $this->serviceRepository->newRecord(
            $request->input('name'),
            $request->user()->id,
            $request->input('category_id')
        );

        if (is_string($service) && $service === Status::ERROR){
            return response()->sendJsonError([], ResponseMessage::ERROR_OCCURRED, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $service->addMedia($request->file('picture'))->toMediaCollection('service_pics');
        if ($request->hasFile('picture_two') && $request->file('picture_two') != null){
            $service->addMedia($request->file('picture_two'))->toMediaCollection('service_pics');
        }

        return response()->sendJsonSuccess([], sprintf(ResponseMessage::CREATE_WAS_SUCCESSFUL, 'Service'), ResponseCode::HTTP_CREATED);
    }

    public function updateService(Request $request, $service_id)
    {
        //dd($request->user()->id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category_id' => 'required|numeric'
        ]);

        if ($validator->fails()){
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response = $this->serviceRepository->update($service_id, $request->only(['name', 'category_id']));

        if ( is_string($response) &&  $response == Status::ERROR ){
            return response()->sendJsonError([], ResponseMessage::ERROR_OCCURRED, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = ['data' => $response];
        return response()->sendJsonSuccess($data, sprintf(ResponseMessage::UPDATE_WAS_SUCCESSFUL, 'Service'), ResponseCode::HTTP_OK);
    }

    public function getMyServices(Request $request)
    {
        $services = $this->serviceRepository->getUserServices($request->user()->id);
        return response()->sendJsonSuccess($services);
    }

    public function destroy($service_id)
    {
        $response = $this->serviceRepository->delete($service_id);

        if ($response == Status::ERROR){
            return response()->sendJsonError([], ResponseMessage::ERROR_OCCURRED);
        }

        return response()->sendJsonSuccess([]);
    }
}
