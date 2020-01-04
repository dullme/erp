<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    //单品管理
    $router->resource('products', ProductController::class);
    $router->get('api/product', 'ProductController@product');

    //组合管理
    $router->resource('composes', ComposeController::class);

    //订单管理
    $router->resource('orders', OrderController::class);

    //库存管理
    $router->resource('warehouses', WarehouseController::class);
    $router->post('api/warehouses', 'WarehouseController@first');


    //供货商
    $router->resource('suppliers', SupplierController::class);
    $router->get('api/supplier', 'SupplierController@supplier');
});
