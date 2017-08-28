<?php

namespace App\Swoole\Handlers;

use App\Note;

class NoteHandler extends BaseHandler
{
    public function __construct()
    {
        //
    }

    public function get($server, $data, $fd)
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

    public function change($server, $data, $fd)
    {
        $room_id = uni_table('users')->get($fd)['room_id'];
        $result = [
            'action' => 'changeNote',
            'message' => $data->message
        ];
        $this->broadcast($server, $room_id, json_encode($result), $fd);
    }

    public function diff($server, $data, $fd)
    {
        // cache note diff to swoole table
        $room_id = uni_table('users')->get($fd)['room_id'];
        uni_table('diffs')->set($room_id, [
            'id' => $fd,
            'content' => $data->message
        ]);
        // merge diff
        if (strlen($data->message) > 500) {
            $server->task([
                'action' => 'mergeDiff',
                'data' => [
                    'note_id' => $room_id,
                    'diff' => $data->message
                ]
            ]);
        }
    }
}