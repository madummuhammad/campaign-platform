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
    $router->post('login', 'AuthController@login');
    $router->get('me', 'AuthController@me');
    $router->get('check_token_forgot', 'AuthController@check_token_forgot');
    $router->post('forgot_password', 'AuthController@forgot_password');
    $router->post('change_forgot_password', 'AuthController@change_forgot_password');
    $router->get('register/verification', 'AuthController@register_verification');
});