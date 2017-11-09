<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Note;

class NoteController extends Controller
{
    public function list()
    {
        $notes = auth()->user()->notes()->paginate();

        return response()->json($notes);
    }

    public function get($id)
    {
        $note = Note::findOrFail($id);
        if ($note->author_id !== auth()->id()) {
            throw new AuthorizationException('Illegal Access');
        }

        return response()->json($note);
    }

    public function create(Request $request)
    {
        $inputs = $request->only(['title']);
        $inputs['author_id'] = auth()->id();
        // create a new empty note
        $note = Note::create($inputs);

        return response()->json($note);
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->intersect(['title', 'alias']);
        $note = Note::findOrFail($id);
        $this->authorize('update', $note);
        // https://laravel.com/docs/5.4/authorization#generating-policies
        // dd(auth()->user()->can('update', $note));
        // update a note
        $note->update($inputs);

        return response()->json($note);
    }

    public function delete($id)
    {
        $note = Note::findOrFail($id);
        if ($note->author_id !== auth()->id()) {
            throw new AuthorizationException('Illegal Access');
        }
        // update a note
        $note->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }
}