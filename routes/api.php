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

$app->group(['prefix' => 'api/v1'], function ($app) {
    $app->get('/', function () use ($app) {
        // $user = \App\User::first();
        // $token = \JWTAuth::fromUser($user);
        // dd($token);
        // $user = \JWTAuth::parseToken()->toUser();
        // dd($user);
        return $app->version();
    });

    $app->group(['middleware' => 'auth:api'], function () use ($app) {
        $app->group(['prefix' => '/prefix'], function () use ($app) {
            dd('success');
        });
    });
});