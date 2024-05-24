<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(["middleware" => ["auth:sanctum"]], function (){
    //profile
   Route::get('profile', [AuthController::class, 'profile']);
   Route::get('logout', [AuthController::class, 'logout']);
});
