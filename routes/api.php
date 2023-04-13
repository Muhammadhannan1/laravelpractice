<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/test', function(){
    p("working");
});

Route::post('user/store','App\Http\Controllers\Api\UserController@store');
Route::post('user/login','App\Http\Controllers\Api\UserController@login');
Route::get('user/get/{flag}',[UserController::class,'index']);
Route::get('user/{id}',[UserController::class,'show']);
Route::delete('user/{id}',[UserController::class,'destroy']);
Route::put('user/update/{id}',[UserController::class,'update']);
