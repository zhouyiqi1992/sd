<?php

/*
|--------------------------------------------------------------------------
| wechat routes
|--------------------------------------------------------------------------
|
| 所有有关小程序端的路由
|
*/

Route::group(['prefix' => 'v1.0', 'namespace' => 'Wechat', 'middleware' => 'responseBodyProcess'], function () {

    /**
     * 登录
     */
    Route::post('/system/login', 'SystemController@login');

    /**
     * 经纬度转地址
     */
    Route::get('/pca/location', 'PCAController@location');

    /**
     * 地址逆转成经纬度
     */
    Route::get('/pca/geoCoder', 'PCAController@geoCoder');

    /**
     * 获取首页轮播图
     */
    Route::get('/banner/listall', 'HomeController@banner');

    /**
     * 测试用登录
     */
    Route::post('/system/self/{id}', 'SystemController@self');

    /**
     * 登陆后执行的操作
     */
    Route::group(['middleware' => 'token:wechat'], function () {

        /**
         * 获取机构列表(列表)
         */
        Route::get('/home/list', 'HomeController@list');

        /**
         * 获取机构列表(地图)
         */
        Route::get('/home/listall', 'HomeController@listall');

        /**
         * 获取机构基本信息
         */
        Route::get('/home/basic/{id}', 'HomeController@basic');

        /**
         * 获取机构详细信息
         */
        Route::get('/home/detail/{id}', 'HomeController@detail');

        /**
         * 获取机构设施设备信息
         */
        Route::get('/home/device/{id}', 'HomeController@device');

        /**
         * 获取机构的评价
         */
        Route::get('/comment/homeList', 'HomeController@commentList');

        /**
         * 对机构进行评价
         */
        Route::post('/comment/save', 'HomeController@commentSave');

        /**
         * 我的页面用户信息
         */
        Route::get('/user/detail', 'UserController@detail');

        /**
         * 用户评论列表
         */
        Route::get('/comment/userList', 'UserController@commentList');

        /**
         * 用户评论列表
         */
        Route::post('/advice/save', 'UserController@advice');


    });
});