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



Route::group([ 'prefix' => 'v1','namespace' => 'Api','middleware' => 'secretAPI'],function () {

    Route::group([ 'prefix' => 'auth'],function () {

        // start login
        Route::post('login','AuthController@login');

    });

        // employee control
        Route::middleware(['auth:api','employee.api'])->group(function(){

            // start category
            Route::apiResource('category','CategoryController');

            // start product
            Route::apiResource('product','ProductController');

            // start product Excel
            Route::post('product-excel','ProductController@productExcel');

            // admin control
            Route::middleware('admin.api')->group(function(){

                // start user
                Route::apiResource('user','UserController');

            });

        });

        // customer control

        Route::group([ 'prefix' => 'auth'],function () {

            // start register
            Route::post('register','AuthCustomerController@register');

            // start reset password
            Route::post('forget-password','AuthCustomerController@forgetPassword');
            Route::post('reset-password','AuthCustomerController@reset');

        });

        Route::middleware(['auth:api','customer.api'])->group(function(){

            Route::get('getCart','CustomerController@getCart');
            Route::post('addCart/{id}','CustomerController@addCart');
            Route::post('editCart/{id}','CustomerController@editCart');
            Route::post('deleteCart/{id}','CustomerController@deleteCart');

        });


        // start logout
        Route::middleware('auth:api')->group(function(){
            Route::post('logout','AuthController@logout');
        });

    });
