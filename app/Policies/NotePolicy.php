<?php

namespace App\Policies;

use App\User;
use App\Note;

class NotePolicy
{
    public function before($user, $ability)
    {
        // if ($user->isSuperAdmin()) {
        //     return true;
        // }
    }

    public function update(User $user, Note $note)
    {
        return $user->id === $note->author_id;
    }
}