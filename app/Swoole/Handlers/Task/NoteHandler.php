<?php

namespace App\Swoole\Handlers\Task;

use App\Note;

class NoteHandler extends BaseHandler
{
    public function __construct()
    {
        //
    }

    public function mergeDiff($data)
    {
        $note = Note::find($data['note_id']);
        $patch = app('dmp')->patch_fromText($data['diff']);
        $result = app('dmp')->patch_apply($patch, $note->content);
        // write to database
        $note->content = $result[0];
        $note->save();
        // clear diff
        uni_table('diffs')->set($data['note_id'], [
            'content' => null
        ]);
        // broadcast updated note
        $result = [
            'room_id' => $data['note_id'],
            'sender' => $data['sender'],
            'message' => json_encode([
                'action' => 'updateNote',
                'message' => $note->content
            ])
        ];
        $this->broadcast($data['server'], $result);
    }
}