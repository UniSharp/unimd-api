<?php

namespace App\Swoole\Handlers\Task;

use Illuminate\Support\Facades\Redis;

class BaseHandler
{
    public function __construct()
    {
        //
    }

    public function broadcast($server, $data)
    {
        $connections = $server->connections;
        $opcode = $data['opcode'] ?? 1;
        $sender = $data['sender'] ?? 0;
        $message = is_array($data['message']) ? json_encode($data['message']) : $data['message'];

        if (!is_null($data['room_id'])) {
            $connections = Redis::smembers(RoomHandler::PREFIX . $data['room_id']);
        }

        foreach($connections as $fd) {
            if ($sender !== (integer) $fd) {
                $server->push($fd, $message, $opcode);
            }
        }
    }
}