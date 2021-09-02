<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedController;


Route::get('/', function(){
    return ['ping' => 'pong'];
    //return response()->json(['error' => 'não encontrado']);
});


Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');        //nao autenticado, sera redirecionado para 401.
Route::post('/user', [AuthController::class, 'create']);         // criando um novo usuario ao banco

Route::post('auth/login', [AuthController::class, 'login']);  //fazer login


Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::put('/user', [UserController::class, 'update']);                  // Alterar os proprios dados //

    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);         // Alterar foto Avatar //

    Route::post('/user/cover', [UserController::class, 'updateCover']);          // Alterar Capa //


          Route::get('/feed', [FeedController::class, 'read']);

          Route::get('/user/feed', [FeedController::class, 'userFeed']);
          Route::get('/user/{id}/feed', [FeedController::class, 'userFeed']);

          Route::get('/user', [UserController::class, 'read']);
          Route::get('/user/{id}', [UserController::class, 'read']);

          Route::post('/feed', [FeedController::class, 'create']);

});

Route::get('show/posts', function(){
    return \App\Models\Post::all();
});

Route::get('show/users', function(){
    return \App\Models\User::all();
});

