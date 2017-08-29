<?php

namespace App\Swoole\Handlers;

use Swoole\WebSocket\Server;
use App\Note;

class NoteHandler extends BaseHandler
{
    protected $maxSyncChars;

    public function __construct()
    {
        $this->maxSyncChars = config('swoole.websocket.max_sync_chars');
    }

    public function get(Server $server, $data, $fd)
    {
        // cache room_id to swoole table
        uni_table('users')->set($fd, [
            'room_id' => $data->note_id
        ]);
        // get note
        $note = Note::find($data->note_id);
        if (!$note) {
            // note not found
            return false;
        }
        $noteResult = [
            'action' => 'getNote',
            'message' => $note->content
        ];
        // put user fd to note room
        $this->joinRoom($server, $data->note_id, $fd);
        // return note
        $server->push($fd, json_encode($noteResult));
        // sync online diffs
        $this->syncDiff($server, $fd, $data->note_id);
    }

    public function change(Server $server, $data, $fd)
    {
        $room_id = uni_table('users')->get($fd)['room_id'];
        $result = [
            'action' => 'changeNote',
            'message' => $data->message
        ];
        $this->broadcast($server, $room_id, json_encode($result), $fd);
    }

    public function diff(Server $server, $data, $fd)
    {
        // cache note diff to swoole table
        $room_id = uni_table('users')->get($fd)['room_id'];
        uni_table('diffs')->set($room_id, [
            'content' => $data->message
        ]);
        // merge diff
        if (strlen($data->message) > $this->maxSyncChars) {
            $server->task([
                'action' => 'mergeDiff',
                'data' => [
                    'server' => $server,
                    'sender' => $fd,
                    'note_id' => $room_id,
                    'diff' => $data->message
                ]
            ]);
        }
    }
}