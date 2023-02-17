<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Note;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('sanctum')->user();
    }

    public function index()
    {
        return response()->json([
            'notes' => $this->user->notes,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param NoteRequest $request
     * @return JsonResponse
     */
    public function store(NoteRequest $request): JsonResponse
    {
        $note = Note::create([
            'user_id' => $this->user->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);
        return response()->json(['data' => $note]);
    }

    /**
     * Display the specified resource.
     *
     * @param Note $note
     * @return JsonResponse
     */
    public function show(Note $note): JsonResponse
    {
        return response()->json(['data' => $note]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param NoteRequest $request
     * @param Note $note
     * @return JsonResponse
     */
    public function update(NoteRequest $request, Note $note): JsonResponse
    {
        $note->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json(['note' => $note]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Note $note
     * @return JsonResponse
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return response()->noContent();
    }
}
