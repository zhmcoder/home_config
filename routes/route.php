<?php

use Illuminate\Routing\Router;

Route::group([
    'domain' => config('deep_admin.route.domain'),
    'prefix' => config('deep_admin.route.api_prefix'),
    'namespace' => '\Andruby\HomeConfig\Controllers',
    'middleware' => config('admin.route.middleware')
], function (Router $router) {
    // 首页配置
    $router->resource('home/config', 'HomeConfigController')->names('home.config');
    $router->resource('search/list', 'SearchController')->names('search.list');
    $router->resource('app/info', 'AppInfoController')->names('app.info');

    $router->get('home/column/relation_grid/{home_column_id}', 'HomeColumnController@relation_grid')->name('home.column.relation_grid');
    $router->get('home/column/relation', 'HomeColumnController@relation')->name('home.category.info');
    $router->resource('home/column', 'HomeColumnController')->names('home.column');

});

