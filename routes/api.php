<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Inscription
Route::post("register",[UserController::class,"register"]);

// Connexion
Route::post("login",[UserController::class,"login"]);

Route::group([ "middleware"=> ["auth:sanctum"]],function(){
    // Profile
Route::get("profile",[UserController::class,"profile"]);

});

Route::middleware('auth:sanctum')->group(function () {
// Envoi de messages
// Route::post('/send-message', [MessageController::class, 'sendMessage']);

Route::post('/send', [MessageController::class, 'sendMessage']);

// Récupération des messages
Route::get('/discussions', [MessageController::class, 'getAllDiscussions']);
Route::get('/messages/{userId}', [MessageController::class, 'getMessages']);
Route::put('/messages/{messageId}', [MessageController::class, 'updateMessage']);
Route::delete('/messages/{messageId}', [MessageController::class, 'deleteMessage']);

Route::patch('messages/read/{id}', [MessageController::class, 'markAsRead']);
Route::post('messages/read/{userId}', [MessageController::class, 'markMessagesAsRead']);

//récupération des utilisateurs
Route::get('/users', [UserController::class, 'getUsers']);

Route::get('/user', [UserController::class, 'getUserDetails']);

// Deconnexion
Route::get("logout",[UserController::class,"logout"]);

});

Route::middleware('auth:sanctum')->group(function () {
    // Liste des posts
    Route::get('posts', [PostController::class, 'index']);

    // Afficher un post spécifique
    Route::get('posts/{id}', [PostController::class, 'show']);

    // Créer un nouveau post
    Route::post('post', [PostController::class, 'store']);

    // Mettre à jour un post spécifique
    Route::put('posts/{id}', [PostController::class, 'update']);

    // Supprimer un post (soft delete)
    Route::delete('posts/{id}', [PostController::class, 'destroy']);

    // Restaurer un post supprimé
    Route::patch('posts/{id}/restore', [PostController::class, 'restore']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
