<?php

namespace App\Http\Controllers\Auth;


use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use App\Constants\ResponseMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Http\Response as ResponseCode;

class PasswordResetController extends Controller
{
    /**
     * Create token to reset password
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()){
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->input('email'))->first();

        if(!$user){
            return response()->sendJsonError([], ResponseMessage::USER_NOT_FOUND, ResponseCode::HTTP_NOT_FOUND);
        }

        $token = Str::random(60);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $request->input('email')],
            [
                'email' => $request->input('email'),
                'token' => $token
            ]
        );

        if ($user && $passwordReset){
            /*
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );
            */
        }

        return response()->sendJsonSuccess(['token' => $token], ResponseMessage::PASSWORD_RESET_LINK_SENT, ResponseCode::HTTP_CREATED);
    }

    /**
     * Find the rest password using token
     * @param string $token
     * @return mixed
     */
    public function find(string $token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset){
            return response()->sendJsonError(
                [],
                sprintf(ResponseMessage::RESOURCE_NOT_FOUND, 'Password reset token'),
                ResponseCode::HTTP_NOT_FOUND
            );
        }

        if(Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()){
            $passwordReset->delete();
            return response()->sendJsonError([], ResponseMessage::PASSWORD_TOKEN_INVALID, ResponseCode::HTTP_NOT_FOUND);
        }

        return response()->sendJsonSuccess($passwordReset);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'token' => 'required|string'
        ]);

        if ($validator->fails()){
            return response()->sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $passwordReset = PasswordReset::where([
            ['token', '=', $request->token],
            ['email', '=', $request->email]
        ])->first();

        if (!$passwordReset){
            return response()->sendJsonError([], ResponseMessage::PASSWORD_TOKEN_INVALID, ResponseCode::HTTP_NOT_FOUND);
        }
        $user = User::where('email', $passwordReset->email)->first();

        if(!$user){
            return response()->sendJsonError([], sprintf(ResponseMessage::RESOURCE_NOT_FOUND, 'User with given email'), ResponseCode::HTTP_NOT_FOUND);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        $passwordReset->delete();

        $user->notify( new PasswordResetSuccess($passwordReset));

        return response()->sendJsonSuccess($user, ResponseMessage::PASSWORD_RESET_SUCCESSFUL, ResponseCode::HTTP_OK);
    }
}
