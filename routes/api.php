<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;


// Inscription
Route::post("register",[UserController::class,"register"]);

// Connexion
Route::post("login",[UserController::class,"login"]);

Route::group([ "middleware"=> ["auth:sanctum"]],function(){
    // Profile
Route::get("profile",[UserController::class,"profile"]);

// Deconnexion
Route::get("logout",[UserController::class,"logout"]);

// Récupération des messages


});

Route::middleware('auth:sanctum')->group(function () {

});
// Envoi de messages
Route::post('/send-message', [MessageController::class, 'sendMessage']);

Route::post('/send', [MessageController::class, 'sendMessage']);


Route::get('/discussions', [MessageController::class, 'getAllDiscussions']);
Route::get('/messages/{userId}', [MessageController::class, 'getMessages']);
Route::put('/messages/{messageId}', [MessageController::class, 'updateMessage']);
Route::delete('/messages/{messageId}', [MessageController::class, 'deleteMessage']);
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
