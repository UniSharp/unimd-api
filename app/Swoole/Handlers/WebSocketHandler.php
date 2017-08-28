<?php

namespace App\Swoole\Handlers;

use Swoole\WebSocket\Server;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Redis;
use App\Swoole\Foundation\Request as SwooleRequest;

class WebSocketHandler extends BaseHandler
{
    protected $dispatchers;
    protected $heartbeatInterval;
    protected $heartbeatPush;
    protected $maxSyncChars;

    public function __construct()
    {
        $this->dispatchers = config('swoole.dispatchers');
        $this->heartbeatInterval = config('swoole.websocket.heartbeat_server_interval') * 1000;
        $this->heartbeatPush = config('swoole.websocket.heartbeat_push');
        $this->maxSyncChars = config('swoole.websocket.maxSyncChars');
    }

    public function onStart(Server $server)
    {
        uni_output("websocket server started: <ws://{$server->host}:$server->port>");
        // clean rooms while unexpected server shutdown
        $this->cleanRooms($server);
    }

    public function onWorkerStart(Server $server)
    {
        // server push heartbeat
        if ($this->heartbeatPush && $server->worker_id === 0) {
            $server->tick($this->heartbeatInterval, function ($id) use ($server) {
                $this->heartbeat($server);
            });
        }
    }

    public function onOpen(Server $server, $request)
    {
        $fd = $request->fd;
        uni_output("server: handshake succeeed with client-{$fd}");
        // tranform request
        $request = SwooleRequest::capture($request);
        // auth user
        $user = auth()->guard('websocket')->setRequest($request)->user();
        if ($user) {
            // cache user data to swoole table
            uni_table('users')->set($fd, ['id' => $user->id, 'name' => $user->name]);
            uni_output("user `{$user->name}`(id: {$user->id}) authenticated"); 
        }
    }

    public function onMessage(Server $server, $frame)
    {
        // decode message
        $data = json_decode($frame->data);

        // filter illegal actions
        if (!isset($data->action)) {
            uni_output("illegal request from client-{$frame->fd} has been denied");
            return false;
        }
        $action = trim($data->action);
        if (!array_key_exists($action, $this->dispatchers)) {
            uni_output("illegal action `{$action}` from client-{$frame->fd} has been denied");
            return false;
        }
        // dispatch by action
        $fd = $frame->fd;
        app()->call($this->dispatchers[$action], compact('server', 'data', 'fd'));
    }

    public function onTask(Server $server, $task_id, $from_id, $data)
    {
        // filter illegal actions
        $action = trim($data['action']);
        if (!array_key_exists($action, $this->dispatchers)) {
            uni_output("illegal task action `{$action}` from client-{$frame->fd} has been denied");
            return false;
        }
        // dispatch by action
        $data = $data['data'];
        app()->call($this->dispatchers[$action], compact('server', 'data', 'task_id', 'from_id'));
    }

    public function onFinish(Server $server, $task_id, $data)
    {
        // task worker callback
    }

    public function onClose(Server $server, $fd)
    {
        uni_output("client-{$fd} is closed");
        // remove user from room
        $user = uni_table('users')->get($fd);
        $this->exitRoom($server, $user['room_id'], $fd);
        // remove user from online users
        uni_table('users')->del($fd);
    }
}