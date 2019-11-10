<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Constants\Status;
use App\Classes\DO_spaces;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as ResponseCode;

class AuthAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'register', 'testDOSave', 'getPic',]);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone_no' => 'required|string|unique:users',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()){
            return Response::sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS,  ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user =  new User([
            'phone_no' => $request->input('phone_no'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'middle_name' => $request->input('middle_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);

        if (!$user->save()){
            return Response::sendJsonError([], ResponseMessage::COULDNT_CREATE_USER, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->sendJsonSuccess([], ResponseMessage::CREATED_USER, ResponseCode::HTTP_CREATED);

    }

    public function login (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_no' => 'required|string|min:11|max:11',
            'password' => 'required|string'
        ]);

        if ($validator->fails()){
            return Response::sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS,  ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credentials = $request->only('phone_no', 'password');
        $credentials['phone_no'] = Str::prefix234ToPhoneNumber($credentials['phone_no']);
        $credentials['status'] = Status::ENABLED;


        // once doesn't generate any sessions or remember user so its good for stateless apps (APIs) over Auth::attempt($credentials).
        if (!Auth::guard('web')->once($credentials)){
            return response()->sendJsonError([], ResponseMessage::INVALID_CREDENTIALS_SUPPLIED, ResponseCode::HTTP_UNAUTHORIZED);
        }

        // TODO :  see if you can implement getSource() and use a oauth client to generate access_token

        $user = $request->user('web');
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($user->remember_token){
            $token->expires_at = Carbon::now()->addMonths();
        }

        $token->save();

        return response()->sendJsonSuccess(
            [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ],
            ResponseMessage::LOGIN_SUCCESSFUL,
            ResponseCode::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->sendJsonSuccess([],ResponseMessage::LOGOUT_SUCCESSFUL ,ResponseCode::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        return response()->sendJsonSuccess($request->user(), Status::SUCCESS, ResponseCode::HTTP_OK);
    }

    /**
     * This action assist with change of password.
     * @param Request $request
     * @return mixed
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6', // this is the current password
            'new_password' => 'required|min:6'
        ]);

        if ($validator->fails()){
            return Response::sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS,  ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credentials = [
            'phone_no' => $request->user()->phone_no,
            'password' => $request->password
        ];

        // check if current password matches
        if (!Auth::attempt($credentials)){
            return response()->sendJsonError([], ResponseMessage::CURRENT_PASSWORD_INVALID, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        // change password
        $user = $request->user();
        $user->password = bcrypt($request->new_password);

        $user->save();

        return response()->sendJsonSuccess([], ResponseMessage::PASSWORD_RESET_SUCCESSFUL, ResponseCode::HTTP_OK);

    }

    /**
     * Assist with creating a user's avatar.
     * @param Request $request
     * @return mixed
     */
    public function uploadAvatar(Request $request)
    {
        // whats returned
        //"SEAKA/PROFILE/xtF4eO7htlESuemnbObErsYQ8L5TSpfQu06PjBGu.png"
        $validator = Validator::make($request->all(), ['avatar' => 'required']);
        if ($validator->fails()){
            return Response::sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS,  ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $request->user();

        $user->addMedia($request->file('avatar'))->toMediaCollection('avatar');

        return response()->sendJsonSuccess([], sprintf(ResponseMessage::PICTURE_UPLOAD_SUCCESSFUL, 'Avatar'), ResponseCode::HTTP_OK);

    }

    /**
     * Assists with getting the current user avatar.
     * @param Request $request
     * @return mixed
     */
    public function  getAvatar(Request $request)
    {
        //dd(Auth::user()->getMedia('avatar')->first()->getPath());
        return Storage::disk('do_spaces')->response(Auth::user()->getMedia('avatar')->first()->getPath());
    }

    public function testDOSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile' => 'required',
        ]);

        if ($validator->fails()){
            return Response::sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS,  ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Storage::disk('do_spaces')->putFile(Constant::SEAKA_PIC_FOLDER, $request->file('profile'));
        //dd(true);

        dd(DO_spaces::saveFile($request->file('profile')));
    }

    public function getPic()
    {
        /*
        // SEAKA/PROFILE/xtF4eO7htlESuemnbObErsYQ8L5TSpfQu06PjBGu.png
        //$full_path = sprintf("%s/SPACE-PRAC/SEAKA/PROFILE/y7TTfVtrrdi91dSvqG71WYXVWkJsMdLTZav6r8bN.png", env('DO_SPACES_ENDPOINT'));
        $full_path ='https:///space-prac.fra1.digitaloceanspaces.com/SPACE-PRAC/SEAKA/PROFILE/xtF4eO7htlESuemnbObErsYQ8L5TSpfQu06PjBGu.png';
        //dd($full_path);
        //dd(Storage::mimeType($full_path));
        $file = DO_spaces::getFile('SEAKA/PROFILE/xtF4eO7htlESuemnbObErsYQ8L5TSpfQu06PjBGu.png');
        dd(File::metaData('SEAKA/PROFILE/xtF4eO7htlESuemnbObErsYQ8L5TSpfQu06PjBGu.png'));
        $headers = [
            'Content-type' => Storage::mimeType($full_path),
        ];
        dump(Storage::mimeType($full_path));
        return response($file, 200, $headers);
        */

        $file = DO_spaces::getFile('SEAKA/PROFILE/xtF4eO7htlESuemnbObErsYQ8L5TSpfQu06PjBGu.png');
        return Storage::disk('do_spaces')->response('SEAKA/PROFILE/xtF4eO7htlESuemnbObErsYQ8L5TSpfQu06PjBGu.png');


    }
}
