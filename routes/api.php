<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PostController;

use App\Http\Controllers\ApiPermissionController;
use App\Http\Controllers\ApiRoleController;
use App\Http\Controllers\ApiUserController;

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
Auth::routes(['verify' => true]);

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    //Route::post('forgotPassword',[PasswordResetRequestController::class],'sendEmail');
    //Route::post('forgotPassword', 'App\Http\Controllers\PasswordResetRequestController@sendEmail');
    Route::post('forgotPassword', [NewPasswordController::class,'forgotPassword']);
    Route::post('resetPassword', [NewPasswordController::class,'reset']);
    //Route::post('resetPassword', 'App\Http\Controllers\ChangePasswordController@passwordResetProcess');
    //Route::post('resetPassword', [ChangePasswordController::class],'passwordResetProcess');
    //Route::post('sendEmail', [EmailVerificationController::class, 'sendVerificationEmail']);
    //Route::get('verifyEmail/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

    Route::get('permissions',[ApiPermissionController::class,'index']);
    Route::post('permissions/create',[ApiPermissionController::class,'store']);
    Route::get('permissions/show/{id}',[ApiPermissionController::class,'show']);
    Route::post('permissions/edit/{id}',[ApiPermissionController::class,'update']);
    Route::post('permissions/delete/{id}',[ApiPermissionController::class,'destroy']);

    Route::get('roles',[ApiRoleController::class,'index']);
    Route::post('roles/create',[ApiRoleController::class,'store']);
    Route::post('roles/show/{id}',[ApiRoleController::class,'show']);
    Route::post('roles/edit/{id}',[ApiRoleController::class,'update']);
    Route::post('roles/delete/{id}',[ApiRoleController::class,'destroy']);

    Route::get('users',[ApiUserController::class,'index']);
    Route::post('users/create',[ApiUserController::class,'store']);
    Route::post('users/show/{id}',[ApiUserController::class,'show']);
    Route::post('users/edit/{id}',[ApiUserController::class,'update']);
    Route::post('users/delete/{id}',[ApiUserController::class,'destroy']);

    Route::get('/approve', [AuthController::class, 'approve']);

});
/*Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('posts', PostController::class); */

Route::post('sendEmail',[MailController::class, 'sendEmail']);




/*Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found or Method Not Supported'], 404);
}); */
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*Route::group(['middleware' => 'api',
'prefix' => 'v1/auth'], function() {


    Route::get('users', [UserController::class, 'index']);

    Route::post('users/create', [UserController::class, 'store']);

    Route::get('users/edit/{id}',[UserController::class, 'edit']);

    Route::patch('users/{id}',[UserController::class, 'update']);

    Route::delete('users/{id}',[UserController::class, 'destroy']);

    Route::get('users/{id}',[UserController::class, 'show']);



    Route::get('roles', [RoleController::class, 'index']);

    Route::post('roles/create', [RoleController::class, 'store']);

    Route::get('roles/edit/{id}',[RoleController::class, 'edit']);

    Route::patch('roles/{id}',[RoleController::class, 'update']);

    Route::delete('roles/{id}',[RoleController::class, 'destroy']);

    Route::get('roles/{id}',[RoleController::class, 'show']);



    Route::get('permissions', [PermissionController::class, 'index']);

    Route::post('permissions/create', [PermissionController::class, 'store']);

    Route::get('permissions/edit/{id}',[PermissionController::class, 'edit']);

    Route::patch('permissions/{id}',[PermissionController::class, 'update']);

    Route::delete('permissions/{id}',[PermissionController::class, 'destroy']);

    Route::get('permissions/{id}',[PermissionController::class, 'show']);



    Route::get('posts', [PostController::class, 'index']);

    Route::post('posts/create', [PostController::class, 'store']);

    Route::get('posts/edit/{id}',[PostController::class, 'edit']);

    Route::patch('posts/{id}',[PostController::class, 'update']);

    Route::delete('posts/{id}',[PostController::class, 'destroy']);

    Route::get('posts/{id}',[PostController::class, 'show']);

}); */


