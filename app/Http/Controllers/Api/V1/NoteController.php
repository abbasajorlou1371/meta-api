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
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('/notes/' . $this->user->id);
        } else {
            $path = "";
        }

        $this->user->notes()->create([
            'title' => $request->title,
            'content' => $request->content,
            'attachment' => $path,
        ]);
        return response()->json([
            'success' => 'یادداشت با موفقیت ثبت شد',
            'notes' => $this->user->notes
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Note $note
     * @return JsonResponse
     */
    public function show(Note $note): JsonResponse
    {
        return response()->json([
            'note' => $note
        ]);
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
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('/notes/' . $this->user->id);
        } else {
            $path = "";
        }

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'attachment' => $path,
        ]);

        return response()->json([
            'success' => 'یادداشت ویرایش شد',
            'notes' => $this->user->notes
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Note $note
     * @return JsonResponse
     */
    public function destroy(Note $note): JsonResponse
    {
        $note->delete();
        return response()->json([
            'success' => 'یادداشت حذف شد'
        ]);
    }
}
