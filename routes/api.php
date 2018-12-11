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

Route::get('/setup', 'API\SetupController@index')->name('setup');
Route::post('/user/register', 'API\AuthController@register')->name('register');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/', function (Request $request) {
        return "API Documentation";
    });
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    //Route::post('/user/update-profile', 'API\UserController@update')->name('update-profile');    // Not implemented
    Route::post('/user/create-loan', 'API\UserController@createLoan')->name('create-loan');
    Route::get('/user/list-payment', 'API\UserController@listPayment')->name('list-payment');
    Route::post('/user/confirm-payment', 'API\UserController@confirmPayment')->name('confirm-payment');
    Route::post('/admin/approve-loan', 'API\AdminController@approveLoan')->name('approve-loan');
    Route::post('/admin/reject-loan', 'API\AdminController@rejectLoan')->name('reject-loan');
    Route::post('/admin/verify-payment', 'API\AdminController@verifyPayment')->name('verify-payment');
});
