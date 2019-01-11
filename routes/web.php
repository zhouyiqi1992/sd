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


Route::group(['prefix' => '/v1.0', 'middleware' => 'responseBodyProcess'], function () {

    Route::get('/index', 'ProductController@index');

    Route::get('/config', 'ProductController@config');

    Route::get('/news', 'ProductController@news');

    //登录
    Route::post('/system/login', 'SystemController@login');

    //用户登出
    Route::get('/system/logout', 'SystemController@logout');

    Route::group(['middleware' => ['token:web', 'requestSave']], function () {

        //首页城市访问量统计
        Route::get('/index/index', 'IndexController@index');

        //首页访问人所在地热度
        Route::get('/index/user', 'IndexController@user');

        //Route::get('/index/index', 'IndexController@index');

        Route::group(['prefix' => '/role'], function () {
            //角色列表
            Route::get('/listall', 'RoleController@list');

            //新增角色
            Route::post('/save', 'RoleController@save');

            //删除角色
            Route::post('/delete/{id}', 'RoleController@delete');

            //添加权限
            Route::post('/permissionSave', 'RoleController@permissionSave');

            //权限列表
            Route::get('/permissionListall', 'RoleController@permissionList');

        });

        Route::group(['prefix' => '/sysuser'], function () {
            //冻结/解冻管理员账号
            Route::post('/freeze', 'SystemController@freeze');

            //管理员列表
            Route::get('/list', 'SystemController@list');

            //新增管理员
            Route::post('/save', 'SystemController@save');

            //删除管理员
            Route::post('/delete/{id}', 'SystemController@delete');

            //修改密码
            Route::post('/authstrreset', 'SystemController@authstrReset');

        });

        Route::group(['prefix' => '/common'], function () {
            //文件上传
            Route::post('/upload', 'CommonController@upload');

            //图片列表
            Route::get('/list/{id}', 'CommonController@list');

            //删除图片
            Route::post('/delete/{id}', 'CommonController@delete');
        });


        Route::get('permission/listall', 'RoleController@permissionListAll');

        Route::group(['prefix' => '/user'], function () {
            //用户列表
            Route::get('/list', 'UserController@list');

            //用户浏览记录
            Route::get('/browselist', 'UserController@browseList');

            //用户留言列表
            Route::get('/advicelist', 'UserController@adviceList');

            //用户评论列表
            Route::get('/commentlist', 'UserController@commentList');

            //评论审核
            Route::post('/commentcheck', 'UserController@commentCheck');
        });

        Route::group(['prefix' => '/home'], function () {
            //机构列表
            Route::get('/list', 'HomeController@list');

            //机构详情
            Route::get('/detail/{id}', 'HomeController@detail');

            //删除机构
            Route::post('/delete/{id}', 'HomeController@delete');

            //修改
            Route::post('/save', 'HomeController@save');

            //冻结/解除冻结机构
            Route::post('/freeze', 'HomeController@freeze');

            //全部冻结/解除冻结
            Route::post('/freezeall', 'HomeController@freezeAll');
        });

        Route::group(['prefix' => '/count'], function () {
            //浏览统计
            Route::get('/browse', 'CountController@browse');

            //评论统计
            Route::get('/comment', 'CountController@comment');
        });

        Route::group(['prefix' => '/banner'], function () {
            //Banner列表
            Route::get('/list', 'BannerController@list');
            //Banner详情
            Route::get('/detail/{id}', 'BannerController@detail');
            //Banner删除
            Route::post('/delete/{id}', 'BannerController@delete');
            //Banner保存
            Route::post('/save', 'BannerController@save');
        });
    });

});

