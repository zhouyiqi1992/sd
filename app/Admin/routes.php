<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('product/create', 'ProductController@create');
    $router->get('product', 'ProductController@index');
    $router->get('product/{id}', 'ProductController@detail');
    $router->post('product', 'ProductController@store');
    $router->post('product/delete/{id}', 'ProductController@store');

    $router->get('categories', 'CategoriesController@index');
    $router->get('categories/create', 'CategoriesController@create');
    $router->get('categories/{id}/edit', 'CategoriesController@edit');
    $router->post('categories', 'CategoriesController@store');
    $router->put('categories/{id}', 'CategoriesController@update');
    $router->delete('categories/{id}', 'CategoriesController@destroy');
    $router->get('api/categories', 'CategoriesController@apiIndex');

    $router->get('news', 'NewsController@index');
    $router->get('news/create', 'NewsController@create');
    $router->get('news/{id}/edit', 'NewsController@edit');
    $router->post('news', 'NewsController@store');
    $router->put('news/{id}', 'NewsController@update');
    $router->delete('news/{id}', 'NewsController@destroy');
    $router->get('news/{id}', 'NewsController@detail');

    $router->get('config', 'ConfigController@index');
    $router->get('config/create', 'ConfigController@create');
    $router->get('config/{id}/edit', 'ConfigController@edit');
    $router->post('config', 'ConfigController@store');
    $router->put('config/{id}', 'ConfigController@update');
    $router->delete('config/{id}', 'ConfigController@destroy');
    $router->get('config/{id}', 'ConfigController@detail');
});
