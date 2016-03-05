<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*==============================================
=            Api Routes - Version 1            =
==============================================*/

Route::group(array('prefix' => 'api/v1'), function()
{
	/* Users */
	Route::post('users/register', 'Api\V1\UserController@register');
	Route::post('users/login', 'Api\V1\UserController@login');
	Route::resource('users', 'Api\V1\UserController');

	/* Ads */
	Route::resource('ads', 'Api\V1\AdController');

	/* Categories */
	Route::resource('categories', 'Api\V1\CategoryController');

	/* Ad Bids */
	Route::post('bids', 'Api\V1\AdBidController@create');

	/* Ad Comments */
	Route::post('comments', 'Api\V1\AdCommentController@create');


});

/* Access Token generate */
Route::post('api/v1/oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});






/*=====  End of Api Routes - Version 1  ======*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::auth();
    Route::get('auth/github', 'Auth\AuthController@redirectToProvider');
    Route::get('auth/github/callback', 'Auth\AuthController@handleProviderCallback');
    Route::get('/home', 'HomeController@index');
});
