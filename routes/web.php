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
        $router->post('token', 'UserController@createToken');
        $router->post('/', 'UserController@create');
        $router->delete('token', 'UserController@destroyToken');
        $router->patch('current', 'UserController@updateCurrent');
        $router->delete('current', 'UserController@destroyCurrent');
    });
    $router->group(['prefix' => 'communities'], function () use ($router) {
        $router->get('search', 'CommunityController@search');
        $router->post('/', 'CommunityController@create');
        $router->get('/', 'CommunityController@index');
        $router->get('{pseudo}', 'CommunityController@show');
        $router->delete('{pseudo}', 'CommunityController@destroy');
        $router->patch('{pseudo}', 'CommunityController@update');
    });
    $router->group(['prefix' => 'texts'], function () use ($router) {
        $router->get('/', 'TextController@index');
        $router->post('/', 'TextController@create');
    });
});
