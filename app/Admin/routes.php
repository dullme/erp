<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->post('/api/change-hq', 'HomeController@changeHq');
    $router->post('/api/change-unit', 'HomeController@changeUnit');
    $router->post('/api/change-weight', 'HomeController@changeWeight');

    //单品管理
    $router->resource('products', ProductController::class);
    $router->get('api/product', 'ProductController@product');

    //组合管理
    $router->resource('composes', ComposeController::class);
    $router->post('api/compose', 'ComposeController@compose');

    //订单管理
    $router->resource('orders', OrderController::class);
    $router->post('api/order/delete/{id}', 'OrderController@deleteOrder');
    $router->post('api/order/finish/{id}', 'OrderController@finishOrder');
    $router->post('api/order-batch-confirm/{id}', 'OrderController@confirmOrderBatch');
    $router->post('api/order-batch-confirm/{id}/delete', 'OrderController@deleteOrderBatch');

    //库存管理
    $router->resource('warehouses', WarehouseController::class);
    $router->post('api/warehouses', 'WarehouseController@first');
    $router->get('api/can-box', 'WarehouseController@canBox');

    //装箱
    $router->resource('packages', PackageController::class);
    $router->get('api/getPackage-info/{id}', 'PackageController@getPackageInfo');
    $router->post('api/package-in', 'PackageController@packageIn');

    //供货商
    $router->resource('suppliers', SupplierController::class);
    $router->get('api/supplier', 'SupplierController@supplier');

    //货代
    $router->resource('forwarding-companies', ForwardingCompanyController::class);
    $router->get('api/forwarding-company', 'ForwardingCompanyController@forwardingCompany');

    //客户
    $router->resource('customers', CustomerController::class);
    //采购商
    $router->resource('buyers', BuyerController::class);
    //港口
    $router->resource('ports', PortController::class);

    //仓储公司
    $router->resource('warehouse-companies', WarehouseCompanyController::class);
    $router->get('api/warehouse-company', 'WarehouseCompanyController@warehouseCompany');
});
