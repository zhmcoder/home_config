<?php

use Illuminate\Routing\Router;

Route::group([
    'domain' => config('deep_admin.route.domain'),
    'prefix' => config('deep_admin.route.api_prefix'),
    'namespace' => '\Andruby\HomeConfig\Controllers',
    'middleware' => config('admin.route.middleware')
], function (Router $router) {
    // 首页配置
    $router->resource('home/item', 'HomeItemController')->names('home.items');
    $router->resource('search/list', 'SearchController')->names('search.list');
    $router->resource('app/info', 'AppInfoController')->names('app.info');

    $router->get('home/config/relation_grid/{home_column_id}', 'HomeColumnController@relation_grid')->name('home.config.relation_grid');
    $router->get('home/config/relation', 'HomeConfigController@relation')->name('home.config.relation');
    $router->resource('home/config', 'HomeConfigController')->names('home.config');

    $router->resource('home/config_type', 'ConfigTypeController')->names('home.config.type');

});

