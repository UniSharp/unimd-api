<?php

namespace App\Swoole\Handlers;

use Swoole\WebSocket\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Swoole\Handlers\Task\RoomHandler;
use Illuminate\Contracts\Encryption\DecryptException;

class BaseHandler
{
    public function __construct()
    {
        //
    }

    protected function broadcast(Server $server, $room_id = null, $message, $sender = null, $opcode = 1)
    {
        $server->task([
            'action' => 'broadcast',
            'data' => [
                'room_id' => $room_id,
                'message' => $message,
                'sender' => $sender,
                'opcode' => $opcode
            ]
        ]);
    }

    protected function heartbeat(Server $server)
    {
        $this->broadcast($server, null, 0, null, 0x9);
    }

    protected function joinRoom(Server $server, $room_id, $fd)
    {
        $server->task([
            'action' => 'joinRoom',
            'data' => [
                'room_id' => $room_id,
                'fd' => $fd
            ]
        ]);
    }

    protected function exitRoom(Server $server, $room_id, $fd)
    {
        $server->task([
            'action' => 'exitRoom',
            'data' => [
                'room_id' => $room_id,
                'fd' => $fd
            ]
        ]);
    }

    protected function cleanRooms()
    {
        $keys = Redis::keys(RoomHandler::PREFIX . '*');
        if (count($keys) > 0) {
            Redis::del($keys);
        }
    }

    protected function syncDiff($server, $fd)
    {
        $note_id = uni_table('users')->get($fd)['room_id'];
        $diff = uni_table('diffs')->get($note_id);
        $diffResult = [
            'action' => 'getDiff',
            'message' => $diff['content']
        ];
        $server->push($fd, json_encode($diffResult));
    }
}