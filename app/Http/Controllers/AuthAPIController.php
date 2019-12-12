<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Constants\Status;
use App\Classes\DO_spaces;
use Illuminate\Support\Str;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as ResponseCode;


/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Seaka Back end Documentation",
 *      description="This holds the documentation to Seaka Back end"
 * )
 *
 */
class AuthAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'register', 'testDOSave', 'getPic',]);
    }

    /**
     * This method checks to see if a role is allowed to be added to the current user.
     * @param User $user
     * @param string $role_name
     * @return bool
     */
    private function canAddRole(User $user, string $role_name)
    {
        // get/RoleNames() => returns a collection normally.
        $roles = $user->getRoleNames()->toArray();

        if (count($roles) > 2 || in_array($role_name, $roles) || in_array(Constant::ADMIN, $roles)){
            return false;
        }

        return true;
    }

    /**
     * Action to create a new user on the app.
     * @param Request $request
     * @return mixed
     */
    /**
     * @OA\Post(
     *     path="/auth/signup",
     *     tags={"register", "signup"},
     *     summary="Add a new to the application.",
     *     operationId="signup",
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input/parameters."
     *     ),
     *
     *     requestBody={"$ref": "#/components/requestBodies/Pet"}
     * )
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone_no' => 'required|string|unique:users',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
            'role' => [
                'required', Rule::in([Constant::ADMIN, Constant::SERVICE_PROVIDER, Constant::CUSTOMER])
            ],
        ]);

        if ($validator->fails()){
            return Response::sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS,  ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        // validate based on the selected roles
        if ($request->input('role') == Constant::ADMIN){

        }

        if ($request->input('role') == Constant::SERVICE_PROVIDER){
            $validator = Validator::make($request->all(), [
                'business_name' => 'required|string|min:2',
                'category' => 'required|integer|exists:categories,id',
                'location' => 'required|string',
                'opening_hours' => 'required|string',
                'general_information' => 'nullable|string',
                'instagram' => 'nullable|string',
                'twitter' =>'nullable|string',
                'linkedin' => 'nullable|url'
            ]);

            if ($validator->fails()){
                return Response::sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS,  ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $user = User::where('phone_no', Str::prefix234ToPhoneNumber ($request->input('phone_no')))->first();

        if (!empty($user)){
            $canAcceptRole = $this->canAddRole($user, $request->input('role'));
            if (!$canAcceptRole){
                return response()->sendJsonError([], ResponseMessage::USER_CANT_TAKE_NEW_ROLE, ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
            }
        }else{
            $user =  new User([
                'phone_no' => Str::prefix234ToPhoneNumber ($request->input('phone_no')),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'middle_name' => $request->input('middle_name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password'))
            ]);
        }

        try{
            DB::beginTransaction();
            // first save to db
            $user->save();

            // second save based on role
            if ($request->input('role') === Constant::SERVICE_PROVIDER){
                ServiceProvider::create([
                    'user_id' => $user->id,
                    'category_id' => $request->input('category'),
                    'business_name' => $request->input('business_name'),
                    'location' => $request->input('location'),
                    'opening_hours' => $request->input('opening_hours'),
                    'general_information' => $request->input('general_information'),
                    'instagram' => $request->input('instagram'),
                    'twitter' => $request->input('twitter'),
                    'linkedin' => $request->input('linkedin'),
                ]);
            }

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return response()->sendJsonError([], ResponseMessage::COULDNT_CREATE_USER, ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        // assign user a role
        $user->assignRole($request->input('role'));

        // TODO: CALL LOGIN ACTION HERE IF NEEDED
        return response()->sendJsonSuccess(['user_id' =>  $user->id], ResponseMessage::CREATED_USER, ResponseCode::HTTP_CREATED);
    }

    /**
     * Action to handle the login operation.
     * @param Request $request
     * @return mixed
     */
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
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                'user_details' => $user->load('serviceProvider', 'roles:id,name'),
            ],
            ResponseMessage::LOGIN_SUCCESSFUL,
            ResponseCode::HTTP_OK
        );
    }

    /**
     * Action to log the current user out.
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->sendJsonSuccess([],ResponseMessage::LOGOUT_SUCCESSFUL ,ResponseCode::HTTP_OK);
    }

    /**
     * Action to get current user details.
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        $user = $request->user()->load('serviceProvider', 'roles:id,name');
        return response()->sendJsonSuccess($user, Status::SUCCESS, ResponseCode::HTTP_OK);
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

        //Config::set('filesystems.disks.do_spaces.bucket', 'space-prac/seaka/profile');
        //Config::set('filesystems.disks.do_spaces.bucket', 'profile_pics');
        $response = $user->addMedia($request->file('avatar'))->toMediaCollection('avatar');
        $avatars = $request->user()->getMedia('avatar');
        //$avatars[count($avatars) - 1]->getFullUrl();
        // TODO : save this url on the users data. create a new column and save it.
        //dd($response);

        return response()->sendJsonSuccess(['url' => $avatars[count($avatars) - 1]->getFullUrl()], sprintf(ResponseMessage::PICTURE_UPLOAD_SUCCESSFUL, 'Avatar'), ResponseCode::HTTP_OK);

    }

    /**
     * Assists with getting the current user avatar.
     * @param Request $request
     * @return mixed
     */
    public function  getAvatar(Request $request)
    {
        //dd(Auth::user()->getMedia('avatar')->first()->getPath());
        //Config::set('filesystems.disks.do_spaces.bucket', 'space-prac/seaka/profile');
        $avatars = $request->user()->getMedia('avatar');
        //dump($avatars[count($avatars) - 1]->getPath());
        //dump($avatars[count($avatars) - 1]->getUrl());
        //dd($avatars[count($avatars) - 1]->getFullUrl());
        return Storage::disk('do_spaces')->response($avatars[count($avatars) - 1]->getPath());
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
