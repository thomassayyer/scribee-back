<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('find', 'UserController@find');
        $router->get('current', 'UserController@showCurrent');
        $router->get('{pseudo}', 'UserController@show');
        $router->post('token', 'UserController@createToken');
        $router->post('/', 'UserController@create');
        $router->delete('token', 'UserController@destroyToken');
    });
    $router->group(['prefix' => 'communities'], function () use ($router) {
        $router->get('search', 'CommunityController@search');
    });
});
