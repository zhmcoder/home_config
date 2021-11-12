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

    $router->get('home/config/relation_grid/{home_config_id}', 'HomeConfigController@relation_grid')->name('home.config.relation_grid');
    $router->get('home/config/relation', 'HomeConfigController@relation')->name('home.config.relation');
    $router->get('home/config/image/{id}/edit', 'HomeConfigController@image')->name('home.config.image');
    $router->put('home/config/save_image/{id}', 'HomeConfigController@save_image')->name('home.config.save_image');
    $router->resource('home/config', 'HomeConfigController')->names('home.config');

    $router->get('home/jump/form_type', 'HomeJumpController@form_type')->name('home.jump.form_type');
    $router->resource('home/jump', 'HomeJumpController')->names('home.jump');

    $router->resource('home/shelf', 'HomeShelfController')->names('home.shelf');

});

