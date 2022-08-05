<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ProfileController extends Controller
{
    // GET USER PROFILE
    public function userProfile (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $email = $user->email;

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $profile = Profile::where('email', $email)->get();

        return response()->json(compact('profile'), 201);
    }

    public function updateProfile (Request $request)
    {

        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $id = $user->id;

            $profile = Profile::find($id);

            $profile->fullname = $request->fullname;
            $profile->phonenumber = $request->phonenumber;
            $profile->gender = $request->gender;
            $profile->gps_long = $request->gps_long;
            $profile->gps_lat = $request->gps_lat;
            $profile->image_link = $request->image_link;
            $profile->id_link = $request->id_link;

            $profile->save();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('profile'), 201);
    }


    public function allProfiles (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;

            if($usertype !== 'admin' && $usertype !== 'auditor'){
                return response()->json([
                    "message" => "You do not have privilege to access this resource!"
                ], 401);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        
        $profiles = Profile::all();

        return response()->json(compact('profiles'), 201);
    }


    public function approveProfile (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;
            $email = $user->email;

            if($usertype !== 'admin' && $usertype !== 'auditor'){
                return response()->json([
                    "message" => "You do not have privilege to access this resource!"
                ], 401);
            }

            $profile = Profile::find($request->id);

            $profile->approval_status = 1;
            $profile->approved_by = $email;

            $profile->save();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $profiles = Profile::all();
        $success = 'User Approved!';

        return response()->json(compact('profiles', 'success'), 201);
    }


    public function disapproveProfile (Request $request)
    {
        try {

            if(! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

            $usertype = $user->usertype;
            $email = $user->email;

            if($usertype !== 'admin' && $usertype !== 'auditor'){
                return response()->json([
                    "message" => "You do not have privilege to access this resource!"
                ], 401);
            }

            $profile = Profile::find($request->id);

            $profile->approval_status = 0;
            $profile->approved_by = $email;

            $profile->save();

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptions $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $profiles = Profile::all();
        $success = 'User Disapproved!';

        return response()->json(compact('profiles', 'success'), 201);
    }
}
