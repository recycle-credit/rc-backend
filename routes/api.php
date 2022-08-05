<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// AUTHENTICATION ROUTES
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate']);
Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [NewPasswordController::class, 'resetPassword']);

Route::group(['middleware' => ['jwt.verify']], function() {
    // USER ROUTES
    Route::get('user', [UserController::class, 'getAuthenticatedUser']);
    Route::post('create-user', [UserController::class, 'createUser']);
    Route::get('all-users', [UserController::class, 'allUsers']);
    Route::post('deactivate', [UserController::class, 'deactivate']);
    Route::post('activate', [UserController::class, 'activate']);

    // PROFILE ROUTES
    Route::get('user-profile', [ProfileController::class, 'userProfile']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);
    Route::get('all-profiles', [ProfileController::class, 'allProfiles']);
    Route::post('approve-profile', [ProfileController::class, 'approveProfile']);
    Route::post('disapprove-profile', [ProfileController::class, 'disapproveProfile']);
});