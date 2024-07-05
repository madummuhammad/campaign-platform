<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group([
    'prefix' => 'auth',
], function ($router) {
    $router->post('register', 'AuthController@register');
    $router->get('caleg_profile', 'AuthController@caleg_profile');
    $router->post('login', 'AuthController@login');
    $router->get('me', 'AuthController@me');
    $router->get('check_token_forgot', 'AuthController@check_token_forgot');
    $router->post('forgot_password', 'AuthController@forgot_password');
    $router->post('change_forgot_password', 'AuthController@change_forgot_password');
    $router->get('register/verification', 'AuthController@register_verification');
});

$router->group([
    'prefix' => 'admin',
], function ($router) {
    $router->post('add', 'AdminController@add');
    $router->get('delete', 'AdminController@delete');
    $router->post('list', 'AdminController@list');
    $router->get('detail', 'AdminController@detail');
    $router->get('get_position', 'AdminController@get_position');
});

$router->group([
    'prefix' => 'dapil',
], function ($router) {
    // Our Data DPR RI
    $router->get('make_dprri', 'DapilController@make_dprri');
    $router->get('get_dapil_dprri','DapilController@getDapilDprri');
    $router->get('make_city_dapil_dprri','DapilController@make_city_dapil_dprri');
    $router->get('get_city_by_dapil_dprri', 'DapilController@getCityByDapilDprri');

    // Our Data DPRD Province
    $router->get('make_dprdprovinsi', 'DapilController@make_dprdprovinsi');
    $router->get('make_city_dprdprovinsi', 'DapilController@make_city_dprdprovinsi');

    // election house data
    $router->get('dprri','DapilController@dprri');

    // election house data
    $router->get('dprdprovinsi','DapilController@dprdprovinsi');

    // Our Party Data
    $router->get('partai','DapilController@partai');
});

$router->group([
    'prefix' => 'dapildprdprovince',
], function ($router) {
    // Our Data DPR RI
    $router->get('get', 'DapildprdprovinceController@get');
    $router->get('get_by_province', 'DapildprdprovinceController@get_by_province');
});


$router->group([
    'prefix' => 'regional',
], function ($router) {
    $router->get('get_province', 'RegionalController@get_province');
    $router->get('get_city', 'RegionalController@get_city');
    $router->get('get_subdistrict', 'RegionalController@get_subdistrict');
    $router->get('get_village', 'RegionalController@get_village');
    $router->get('make_province', 'RegionalController@make_province');
    $router->get('make_city', 'RegionalController@make_city');
    $router->get('make_subdistrict', 'RegionalController@make_subdistrict');
    $router->get('make_village', 'RegionalController@make_village');
});



