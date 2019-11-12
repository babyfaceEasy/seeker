<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Response as ResponseCode;

class UserController extends Controller
{

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Action to enable a new user.
     * @param Request $request
     * @return mixed
     */
    public function enableUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()){
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::find($request->input('id'));

        if (empty($user)){
            return response()->sendJsonError([], ResponseMessage::USER_NOT_FOUND, ResponseCode::HTTP_NOT_FOUND);
        }

        $repositoryResponse = $this->userRepository->enableUser($user);

        if ($repositoryResponse == Status::SUCCESS){
            return response()->sendJsonSuccess();
        }

        return response()->sendJsonError([], ResponseMessage::ERROR_OCCURRED, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);

    }
}