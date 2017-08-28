<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Auth\Guards\JwtAuthGuard;
use App\Swoole\Auth\Guards\WebsocketGuard;

class JwtAuthGuardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->extend('jwt-auth', function ($app, $name, array $config) {
            $guard = new JwtAuthGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });

        $this->app['auth']->extend('websocket', function ($app, $name, array $config) {
            $guard = new WebsocketGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );
            return $guard;
        });
    }
}
