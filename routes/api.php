<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});




Route::prefix('auth')->group(function(){
    Route::post('/user/login','UserController@login');
    Route::post('/user/register', 'UserController@register');

    Route::group(['middleware' => 'auth:api'], function(){
        Route::get('/user', 'UserController@get_user');
        Route::get('/user/logout', 'UserController@logout');
    });

});

Route::group(['middleware' => 'auth:api'], function(){

    Route::get('/destination/get', 'DestinationController@index');
    Route::post('/destination/create', 'DestinationController@create');

});

