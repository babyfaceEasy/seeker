<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('test-upload', 'AuthAPIController@uploadAvatar');
Route::get('get-pic', 'AuthAPIController@getPic');

// v1
Route::group(['prefix' => '/v1'], function (){
    // auth routes
    Route::group(['prefix' => '/auth'], function (){
        Route::post('/login', 'AuthAPIController@login');
        Route::post('/signup', 'AuthAPIController@register');
        Route::post ('/user/upload', 'AuthAPIController@uploadAvatar');
        Route::get('/user/upload', 'AuthAPIController@getAvatar');
        Route::get('/logout', 'AuthAPIController@logout');
        Route::get('/user', 'AuthAPIController@user');
        Route::put('/password', 'AuthAPIController@changePassword');
    });

    Route::group(['prefix' => '/category'], function () {
        Route::get('', 'CategoryController@index');
        Route::get('/{category_id}', 'CategoryController@show');
        Route::put('/{category_id}', 'CategoryController@update');
        Route::delete('/{category_id}', 'CategoryController@destroy');
    });

    Route::group(['prefix' => '/user'], function(){
        Route::post('/enable', 'UserController@enableUser');
    });

    Route::group(['prefix' => '/services'], function (){
       Route::get('/', 'ServiceController@index');
       Route::post('/', 'ServiceController@create');
    });

    Route::group(['namespace' => 'Auth', 'prefix' => '/password'], function() {
        Route::post('/create', 'PasswordResetController@create');
        Route::post('/reset', 'PasswordResetController@reset');
        Route::get('/find/{token}', 'PasswordResetController@find');
    });

    Route::group(['prefix' => '/providers', 'middleware' => ['auth:api', 'role:service_provider']], function (){
        Route::post('/services', 'ServiceProviderController@createService');
    });
});
