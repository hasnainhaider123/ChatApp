<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubscriptionController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class,'login'])->name("login");
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
});

/////////////User Data /////////////////////////////
Route::middleware(['auth:api'])->group(function () {


    Route::get('getUserByID/{id}',[UserController::class,'getLoginUserDataByID'])->name('getUserById')->middleware(['auth:api']);
    Route::get('login-user', [UserController::class,'userProfile']);

////////////////////////Search route start ///////////////////

    Route::post('history/save',[SearchController::class,'saveSearch']);
    Route::get('get-history/{days}',[SearchController::class,'getHistory']);

////////////////////////////End Search Route
///

    Route::post('subscription/save',[SubscriptionController::class,'paySubscription']);

});


/////////////register route///////////

Route::post('register',[UserController::class,'register'])->name('register');
////////////////////////////

