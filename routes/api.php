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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix' => 'v1'
    ], function () {
    //auth
        Route::post('login', 'Api\V1\AuthController@login');
        Route::post('register', 'Api\V1\AuthController@register');
        Route::get('logout', 'Api\V1\AuthController@logout');
        Route::get('userinfo', 'Api\V1\AuthController@user');
    //
    Route::apiResource('product', 'Api\V1\ProductController');
    Route::post('product/search/{name}', 'Api\V1\ProductController@search');

    Route::apiResource('stock', 'Api\V1\StockController');

    Route::apiResource('cart', 'Api\V1\CartController');

    Route::apiResource('order', 'Api\V1\OrderController');
    Route::middleware('checkadmin:auth:api')->post('order/date', 'Api\V1\OrderController@orderDate');


});
