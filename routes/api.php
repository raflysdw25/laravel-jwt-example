<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\JwtAuthController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api', 'prefix' => 'auth', 'namespace' => 'App\Http\Controllers'], function ($router) {
    Route::post('/signup', 'JwtAuthController@register');
    Route::post('/signin', 'JwtAuthController@login');
    Route::get('/user', 'JwtAuthController@user');
    Route::post('/token-refresh', 'JwtAuthController@refresh');
    Route::post('/signout', 'JwtAuthController@signout');

});

Route::group(['middleware' => 'api', 'namespace' => 'App\Http\Controllers'], function ($router) {
   Route::resource('todos', 'TodoController');

});