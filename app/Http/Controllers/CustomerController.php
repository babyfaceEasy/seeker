<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use Illuminate\Http\Request;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Response as ResponseCode;
use App\Repositories\ServiceRepositoryInterface;

class CustomerController extends Controller
{

    /**
     * @var ServiceRepositoryInterface
     */
    private $serviceRepository;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository, UserRepositoryInterface $userRepository)
    {
        $this->serviceRepository = $serviceRepository;
        $this->userRepository = $userRepository;
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
        return response()->sendJsonSuccess($services);
    }

    public function savedServices(Request $request)
    {
        $user = $request->user();
        $data = $this->userRepository->getSavedServices($user);
        return response()->sendJsonSuccess($data);

    }

    public function saveService(Request $request)
    {
        $validator = Validator::make($request->all(), ['service_id' => 'required|int']);
        if ($validator->fails()){
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }


        $service = $this->serviceRepository->findByID($request->input('service_id'));
        if (empty($service)){
            return response()->sendJsonError([], sprintf(ResponseMessage::RESOURCE_NOT_FOUND, "Service"), ResponseCode::HTTP_NOT_FOUND);
        }
        $user = $request->user();


        $repositoryResponse = $this->userRepository->saveService($user, $service);

        if ($repositoryResponse != Status::SUCCESS){
            return response()->sendJsonError([], ResponseMessage::ERROR_OCCURRED, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->sendJsonSuccess([], sprintf(ResponseMessage::RESOURCE_WAS_SUCCESSFUL, "Save service"), ResponseCode::HTTP_CREATED);

    }

    public function removeService(Request $request)
    {
        $validator = Validator::make($request->all(), ['service_id' => 'required|int']);
        if ($validator->fails()){
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }
        $service = $this->serviceRepository->findByID($request->input('service_id'));
        if (empty($service)){
            return response()->sendJsonError([], sprintf(ResponseMessage::RESOURCE_NOT_FOUND, "Service"), ResponseCode::HTTP_NOT_FOUND);
        }

        $user = $request->user();
        $repoResposnse = $this->userRepository->removeService($user, $service);

        if ($repoResposnse !== Status::SUCCESS){
            return response()->sendJsonError([], ResponseMessage::ERROR_OCCURRED, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->sendJsonSuccess([], sprintf(ResponseMessage::RESOURCE_WAS_SUCCESSFUL, "Service removal"), ResponseCode::HTTP_OK);
    }

}
