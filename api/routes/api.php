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

//\App\Http\Controllers\FeedController::

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');        //se for acessar alguma rota ou usar algum metodo e nao estiver logado ele joga pra 401.
Route::post('/user', [AuthController::class, 'create']);         // adicionando um novo usuario ao banco

Route::post('auth/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::put('/user', [UserController::class, 'update']);                  // Alterar os proprios dados //

    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);         // Alterar foto Avatar //

    Route::post('/user/cover', [UserController::class, 'updateCover']);          // Alterar Capa //

    /*
          Route::get('/feed', 'FeedController@read');
          Route::get('/user/feed', 'FeedController@userFeed');
          Route::get('/user/{ id }/feed', 'FeedController@userFeed');

          Route::get('/user', 'UserController@read');
          Route::get('/user/{ id }', 'UserController@read');
*/
          //primeiro criar os feeds, para depois ser mostrados e alterados
          //Route::post('/feed', 'FeedController@create');
          Route::get('/feed', [FeedController::class, 'read']);

          Route::get('/user/feed', [FeedController::class, 'userFeed']);
          Route::get('/user/{id}/feed', [FeedController::class, 'userFeed']);

          Route::get('/user', [UserController::class, 'read']);
          Route::get('/user/{id}', [UserController::class, 'read']);

          Route::post('/feed', [FeedController::class, 'create']);

    /*
              Route::post('/post/{ id }/like', 'PostController@like');
              Route::post('/post/{ id }/comment', 'PostController@comment');

              Route::get('/search', 'SearchController@search');
          */
});



//Route::post('auth/login', 'AuthController@login');          // NOT AUTH//
//Route::post('auth/logout', 'AuthController@logout');       // nao precisa mandar nada
//Route::post('auth/refresh', 'AuthController@refresh');    // recriar o token


/*
Route::put('/user', 'UserController@update');                        // Alterar os proprios dados
Route::post('/user/avatar', 'UserController@updateAvatar');         // Alterar foto Avatar
Route::post('/user/cover', 'UserController@updateCover');          // Alterar Capa

Route::get('/feed', 'FeedController@read');
Route::get('/user/feed', 'FeedController@userFeed');
Route::get('/user/{ id }/feed', 'FeedController@userFeed');

Route::get('/user', 'UserController@read');
Route::get('/user/{ id }', 'UserController@read');

Route::post('/feed', 'FeedController@create');

Route::post('/post/{ id }/like', 'PostController@like');
Route::post('/post/{ id }/comment', 'PostController@comment');

Route::get('/search', 'SearchController@search');

*/

Route::get('show/posts', function(){
    return \App\Models\Post::all();
});

Route::get('show/users', function(){
    return \App\Models\User::all();
});

