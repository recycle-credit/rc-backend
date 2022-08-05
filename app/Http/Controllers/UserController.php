<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {

            if(! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }

            $token = JWTAuth::attempt($credentials);

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $usertype = JWTAuth::user()->usertype;
        //return response()->json(compact('token'));
        return response()->json(compact('usertype', 'token'), 201);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usertype' => 'required|string|max:10',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'usertype' => $request->get('usertype'),
            'name' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'created_by' => 'self',
            'status' => 1,
        ]);

        $profile = Profile::create([
            'usertype' => $request->get('usertype'),
            'email' => $request->get('email'),
            'fullname' => '',
            'phonenumber' => '',
            'gender' => '',
            'gps_long' => '',
            'gps_lat' => '',
            'image_link' => '',
            'id_link' => '',
            'approval_status' => 0,
            'approved_by' => ''
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }


    public function getAuthenticatedUser()
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }


    public function createUser (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usertype' => 'required|string|max:10',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;
            $email = $user->email;

            if($usertype !== 'admin'){
                return response()->json([
                    "message" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $user = User::create([
                'usertype' => $request->get('usertype'),
                'name' => $request->get('username'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'created_by' => $email,
                'status' => 1
            ]);
    
            $profile = Profile::create([
                'usertype' => $request->get('usertype'),
                'email' => $request->get('email'),
                'fullname' => '',
                'phonenumber' => '',
                'gender' => '',
                'gps_long' => '',
                'gps_lat' => '',
                'image_link' => '',
                'id_link' => '',
                'approval_status' => 0,
                'approved_by' => ''
            ]);


        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $users = User::all();
        $success = 'User Created Successfully!';

        return response()->json(compact('user', 'success'), 201);
    }


    public function allUsers (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;

            if($usertype !== 'admin'){
                return response()->json([
                    "message" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $users = User::all();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('users'), 201);
    }


    public function deactivate (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;

            if($usertype !== 'admin'){
                return response()->json([
                    "message" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $userinfo = User::find($request->id);
            $pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 9);

            $userinfo->password = Hash::make($pass);
            $userinfo->status = 0;
            $userinfo->save();

            $users = User::all();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $message = 'User Account Deactivated!';
        return response()->json(compact('users', 'message'), 201);
    }


    public function activate (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;

            if($usertype !== 'admin'){
                return response()->json([
                    "message" => "You do not have the permission to access this resource!"
                ], 401);
            }

            $userinfo = User::find($request->id);
            $email = $userinfo->email;

            // SET USER TO ACTIVE
            $userinfo->status = 1;
            $userinfo->save();

            // SEND PASSWORD RESET LINK TO USER EMAIL
            $status = Password::sendResetLink(
                ['email' => $email]
            );
    
            if($status == Password::RESET_LINK_SENT) {
                return [
                    'status' => __($status)
                ];
            }
    
            throw Exception::withMessages([
                'email' => [trans($status)],
            ]);

            $users = User::all();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $message = 'User Account Activated! Password Resent link sent to user email.';
        return response()->json(compact('users', 'message'), 201);
    }


}
