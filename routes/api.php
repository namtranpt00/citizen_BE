<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\HamletController;
use App\Http\Controllers\CitizenController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', [AuthController::class, 'login']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    //user
    Route::post('register', [AuthController::class, 'register']);
    Route::post('user/update/{id}', [AuthController::class, 'update_user']);
    Route::delete('user/{id}', [AuthController::class, 'delete_user']);
    Route::post('logout', [AuthController::class, 'logout']);
    //Tỉnh
    Route::resource('province', ProvinceController::class);
    Route::post('province/list', [ProvinceController::class, 'list']);
    //Huyện
    Route::resource('district', DistrictController::class);
    Route::post('district/list', [DistrictController::class, 'list']);
    //Xã
    Route::resource('ward', WardController::class);
    Route::post('ward/list', [WardController::class, 'list']);
    //Thôn
    Route::resource('hamlet', HamletController::class);
    Route::post('hamlet/list', [HamletController::class, 'list']);
    //Người dân
    Route::resource('citizen', CitizenController::class);
    Route::post('citizen/list', [CitizenController::class, 'list']);
});
