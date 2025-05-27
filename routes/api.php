<?php

use App\Http\Controllers\Api\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ServiceInformationController;
use App\Http\Controllers\Api\VideoController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'Register');
    Route::post('login', 'login');
    Route::get('profile', 'profile')->middleware('auth:sanctum');
    Route::get('logout', 'logout')->middleware('auth:sanctum');
    Route::post('changePassword', 'changePassword')->middleware('auth:sanctum');
    Route::post('changePhoneNumber','changePhoneNumber')->middleware('auth:sanctum');
});

Route::post('upload_image/{Id}',[ImageController::class,'uploadImage'])->middleware('auth:sanctum');
Route::delete('delete_image/{id}',[ImageController::class,'destroy'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function (){

    Route::apiResource('chat', \App\Http\Controllers\Api\ChatController::class)->only(['index','store','show']);
    Route::apiResource('chat_message', \App\Http\Controllers\Api\ChatMessageController::class)->only(['index','store']);
    Route::apiResource('user', UserController::class)->only(['index']);

});


route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('service', ServiceController::class);
    Route::apiResource('serviceinformation', ServiceInformationController::class);
});
Route::controller(AdminController::class)->group(function(){
    Route::get('get_all_provider','get_all_provider');
    Route::get('get_service','get_service');
    Route::get('get_masseges','get_masseges');
});
Route::controller(VideoController::class)->group(function(){
    Route::post('store_video/{id}', 'store')->middleware('auth:sanctum');
    Route::delete('delete_video/{id}', 'destroy')->middleware('auth:sanctum');
});
Route::controller(LocationController::class)->group(function(){
    Route::post('store_location', 'store')->middleware('auth:sanctum');
    Route::put('update_location/{id}', 'update')->middleware('auth:sanctum');
    Route::delete('delete_location/{id}','delete_location')->middleware('auth:sanctum');
    
});


