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

$app->group(['prefix' => 'api/v1', 'namespace' => 'API\V1'], function ($app) {
    $app->get('/', function () use ($app) {
        return $app->version();
    });

    $app->post('/register', ['as' => 'auth.register', 'uses' => 'AuthController@register']);
    $app->post('/login', ['as' => 'auth.login', 'uses' => 'AuthController@login']);
    $app->post('/logout', ['as' => 'auth.logout', 'uses' => 'AuthController@logout']);

    $app->group(['prefix' => '/prefix', 'middleware' => 'auth:api'], function () use ($app) {
        $app->get('/', function () use ($app) {
            dd(auth()->user());
        });
    });
});