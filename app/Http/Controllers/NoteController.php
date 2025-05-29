<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteRequest;
use App\Http\Requests\PartialNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class NoteController extends Controller
{
    public function validate(NoteRequest|PartialNoteRequest $request, $keys = ["title", "note"])
    {
        return $request->only($keys);
    }
    public function parseUserToken($token)
    {
        $token = PersonalAccessToken::findToken($token);
        return $token->tokenable_id;
    }
    public function create(NoteRequest $request)
    {
        $userId = $this->parseUserToken($request->header("token"));
        $note = $this->validate($request);
        $note["user_id"] = $userId;

        try {
            $note = Note::create($note);
        } catch (\Throwable $th) {
            return response([
                "message" => "Internal server error !"
            ], 500);
        }

        return response([
            "message" => "Note created successfuly",
            "note" => new NoteResource($note)
        ], 201);
    }
    public function update(PartialNoteRequest $request, $id)
    {
        $newNote = $this->validate($request);
        $userId = $this->parseUserToken($request->header("token"));
        $note = Note::where("user_id", $userId)->where("id", $id)->first();
        if (!$note) 
            return response(["message" => "Note not found !"], 404);
        
        try {
            $note->update($newNote);
        } catch (\Throwable $th) {
            return response(["message" => "Internal server error !"], 500);
        }

        return response([
            "message" => "Note updated successfuly",
            "note" => new NoteResource($note)
        ], 200);
    }
    public function delete(Request $request, $id)
    {
        $userId = $this->parseUserToken($request->header("token"));
        $note = Note::where("user_id", $userId)->where("id", $id)->first();
        
        if (!$note)
            return response(["message" => "Note not found !"], 404);
        
        try {
            $note->delete();
        } catch (\Throwable $th) {
            return response(["message" => $th->getMessage()], 500);
        }
        
        return response(["message" => "Note deleted successfuly"], 200);
    }
}
