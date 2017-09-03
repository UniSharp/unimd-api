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

    // authentication
    $app->post('/register', ['as' => 'auth.register', 'uses' => 'AuthController@register']);
    $app->post('/login', ['as' => 'auth.login', 'uses' => 'AuthController@login']);
    $app->post('/logout', ['as' => 'auth.logout', 'uses' => 'AuthController@logout']);

    // auth protected routes
    $app->group(['prefix' => '/note', 'middleware' => 'auth:api'], function () use ($app) {
        // notes
        $app->get('/list', ['as' => 'note.list', 'uses' => 'NoteController@list']);
        $app->get('/{note}', ['as' => 'note.get', 'uses' => 'NoteController@get']);
        $app->post('/create', ['as' => 'note.create', 'uses' => 'NoteController@create']);
        $app->put('/{note}', ['as' => 'note.update', 'uses' => 'NoteController@update']);
        $app->delete('/{note}', ['as' => 'note.delete', 'uses' => 'NoteController@delete']);
    });
});