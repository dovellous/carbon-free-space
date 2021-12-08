<?php

if(!isset($routes))
{
    $routes = \Config\Services::routes(true);
}


//$routes->setDefaultNamespace('index');
//$routes->setDefaultController('Acme\Core\System\Modules\Auth\Users\Controllers\Login');
//$routes->setDefaultMethod('index');
//$routes->set404Override('Acme\System\Users\Controllers\Http::s404');

$routes->group('/', ['namespace' => 'App\Controllers'], function($subroutes){

    /*** Route for Pages ***/
    $subroutes->add('', 'Pages::index');
    $subroutes->add('/', 'Pages::index');
    $subroutes->add('home', 'Pages::index');
    $subroutes->add('homepage', 'Pages::homepage');
    $subroutes->add('about', 'Pages::about_us');
    $subroutes->add('about-us', 'Pages::about_us');
    $subroutes->add('about_us', 'Pages::about_us');
    $subroutes->add('food', 'Pages::products');
    $subroutes->add('our-food', 'Pages::products');
    $subroutes->add('our_food', 'Pages::products');
    $subroutes->add('menu', 'Pages::menu');
    $subroutes->add('our-menu', 'Pages::menu');
    $subroutes->add('our_menu', 'Pages::menu');
    $subroutes->add('promos', 'Pages::promos');
    $subroutes->add('promotions', 'Pages::promotions');
    $subroutes->add('careers', 'Pages::jobs');
    $subroutes->add('contacts', 'Pages::contact_us');
    $subroutes->add('contact-us', 'Pages::contact_us');
    $subroutes->add('contact_us', 'Pages::contact_us');
    $subroutes->add('terms', 'Pages::terms');
    $subroutes->add('privacy', 'Pages::privacy');
    $subroutes->add('cookie-policy', 'Pages::cookie_policy');
    $subroutes->add('cookie_policy', 'Pages::cookie_policy');
    $subroutes->add('declaimers', 'Pages::declaimers');
    $subroutes->add('page/(:any)', 'Page::view/$1');
    $subroutes->add('platform', 'Platform::index');

});

$routes->group('auth', ['namespace' => 'Acme\System\Modules\Auth\Controllers'], function($subroutes){

    /*** Route for Auth ***/
    $subroutes->add('', 'Auth::login');
    $subroutes->add('/', 'Auth::login');
    $subroutes->add('/login', 'Auth::login');
    $subroutes->add('register', 'Auth::register');
    $subroutes->add('logout', 'Auth::logout');
    $subroutes->add('authenticate-user', 'Auth::authenticate_user');
    $subroutes->add('password-reset', 'Auth::password_reset');
    $subroutes->add('account-recover', 'Auth::account_recover');
    $subroutes->add('email-confirmation', 'Auth::email_confirmation');
    $subroutes->add('phone-confirmation', 'Auth::email_confirmation');

});
