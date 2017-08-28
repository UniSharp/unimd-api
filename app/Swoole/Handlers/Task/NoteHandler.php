<?php

namespace App\Swoole\Handlers\Task;

use App\Note;

class NoteHandler
{
    public function __contruct()
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
    }
}