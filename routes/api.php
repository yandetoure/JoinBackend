<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


// Inscription
Route::post("register",[UserController::class,"register"]);

// Connexion
Route::post("login",[UserController::class,"login"]);

Route::group([ "middleware"=> ["auth:sanctum"]],function(){
    // Profile
Route::get("profile",[UserController::class,"profile"]);

// Deconnexion
Route::get("logout",[UserController::class,"logout"]);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
