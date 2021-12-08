<?php

if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}

$routes->group('/dashboard', ['namespace' => '\Acme\Modules\Dashboard\Controllers'], function($routes){

    /*** Route for Auth ***/
    $routes->add('', 'Dashboard::index');
    $routes->add('/', 'Dashboard::index');
    $routes->match(['get', 'post'], 'index', 'Dashboard::index');
    $routes->match(['get', 'post'], 'home', 'Dashboard::index');
    $routes->match(['get', 'post'], 'orders/(:any)', 'Dashboard::orders/$1');
    $routes->match(['get', 'post'], 'order_details/(:any)', 'Dashboard::order_details/$1');
    $routes->match(['get', 'post'], 'order_details_update_status/(:any)', 'Dashboard::order_details_update_status/$1');
    $routes->match(['get', 'post'], 'order_json/(:any)', 'Dashboard::order_json/$1');
    $routes->match(['get', 'post'], 'order_preview/(:any)', 'Dashboard::order_preview/$1');
    $routes->match(['get', 'post'], 'orders_update_status/(:any)', 'Dashboard::orders_update_status/$1');
    $routes->match(['get', 'post'], 'orders_update_driver/(:any)', 'Dashboard::orders_update_driver/$1');
    $routes->match(['get', 'post'], 'orders_map/(:any)', 'Dashboard::orders_map/$1');
    $routes->match(['get', 'post'], 'orders_add_payment/(:any)', 'Dashboard::orders_add_payment/$1');

});
