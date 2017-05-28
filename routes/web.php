<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'user'], function () {
    Route::get('active', 'UserController@active');
    Route::get('profile', 'UserController@profile');
    Route::post('profile', 'UserController@save_profile');
    Route::post('discount', 'DiscountController@find');
    Route::post('login', 'UserController@login');
    Route::post('register', 'UserController@register');
    Route::post('logout', 'UserController@logout');
    Route::post('password/reset', 'UserController@region_reset_password');
});

Route::group(['prefix' => 'region', 'middleware' => 'login'], function () {
    Route::get('/', 'RegionController@all');
    Route::get('detail', 'RegionController@detail');
});

Route::group(['prefix' => 'commodity', 'middleware' => 'login'], function () {
    Route::get('detail', 'CommodityController@detail');
    Route::post('buy', 'CommodityController@buy');
});

Route::group(['prefix' => 'admin', 'middleware' => ['login', 'admin']], function () {
    Route::group(['prefix' => 'user'], function () {
        // 用户列表
        Route::get('/', 'UserController@lists');
        // 获得用户在某个区域中的信息
        Route::get('region', 'UserController@region');
        // 获得用户的详细信息，在所有区域中的信息
        Route::get('detail', 'UserController@detail');
        Route::get('record', 'UserController@record_search');
        // 启用或禁用指定用户在某个区域中的账户
        Route::get('enable', 'UserController@enable');
        Route::post('create', 'UserController@create');
        // 编辑用户信息
        Route::post('profile', 'UserController@admin_profile_edit');
        // 编辑用户在指定区域中的账户
        Route::post('region_user', 'UserController@admin_save_region_user');
        Route::post('region_user/delete', 'UserController@admin_delete_region_user');
    });
    Route::group(['prefix' => 'region'], function () {
        Route::get('detail', 'RegionController@admin_detail');
        Route::post('create', 'RegionController@create');
        Route::post('update', 'RegionController@update');
        Route::post('delete', 'RegionController@delete');
    });
    Route::group(['prefix' => 'server'], function () {
        Route::get('/', 'ServerController@search');
        Route::get('detail', 'ServerController@detail');
        Route::post('create', 'ServerController@create');
        Route::post('update', 'ServerController@update');
        Route::post('delete', 'ServerController@delete');
    });
    Route::group(['prefix' => 'commodity'], function () {
        Route::get('/', 'CommodityController@search');
        Route::get('detail', 'CommodityController@admin_detail');
        Route::post('create', 'CommodityController@create');
        Route::post('update', 'CommodityController@update');
        Route::post('delete', 'CommodityController@delete');
    });
    Route::group(['prefix' => 'discount'], function () {
        Route::get('/', 'DiscountController@search');
        Route::post('delete', 'DiscountController@delete');
        Route::post('create', 'DiscountController@create');
    });
});