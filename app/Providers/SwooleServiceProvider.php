<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Swoole\WebSocket;
use App\Swoole\Table;
use Swoole\WebSocket\Server;

class SwooleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('websocket', function ($app) {
            $server = new Server(config('swoole.websocket.server'), config('swoole.websocket.port'));
            $server->set(config('swoole.settings'));
            return $server;
        });

        $this->app->singleton('swoole.table', function ($app) {
            return new Table;
        });

        $this->app->singleton('output', function ($app) {
            return $app->make(ConsoleOutput::class);
        });
    }
}
