<?php

use App\Http\Controllers\ApiPermissionController;
use App\Http\Controllers\ApiRoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */


Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'

], function ($router) {

    Route::get('permissions',[ApiPermissionController::class,'index']);
    Route::post('permissions/createPermission',[ApiPermissionController::class,'createPermission']);
    Route::post('permissions/showPermission/{id}',[ApiPermissionController::class,'showPermission']);
    Route::post('permissions/updatePermission/{id}',[ApiPermissionController::class,'updatePermission']);
    Route::post('permissions/deletePermission/{id}',[ApiPermissionController::class,'deletePermission']);

    Route::get('roles',[ApiRoleController::class,'index']);
    Route::post('roles/createRole',[ApiRoleController::class,'createRole']);
    Route::post('roles/showRole/{id}',[ApiRoleController::class,'showRole']);
    Route::post('roles/updateRole/{id}',[ApiRoleController::class,'updateRole']);
    Route::post('roles/deleteRole/{id}',[ApiRoleController::class,'deleteRole']);

    Route::get('users',[ApiUserController::class,'index']);
    Route::post('users/createUser',[ApiUserController::class,'createuser']);
    Route::post('users/showUser/{id}',[ApiUserController::class,'showuser']);
    Route::post('users/updateUser/{id}',[ApiUserController::class,'updateuser']);
    Route::post('users/deleteUser/{id}',[ApiUserController::class,'deleteuser']);
});
