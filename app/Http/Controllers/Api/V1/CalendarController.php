<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Http\Resources\EventResource;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('interact');
    }

    /**
     * Display a listing of the events.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'event');
        $eventsQuery = Calendar::query();

        switch ($type) {
            case 'version':
                $eventsQuery = $eventsQuery->versionEvents();
                break;
            case 'event':
            default:
                $eventsQuery = $eventsQuery->currentEvents();
                break;
        }

        $events = $eventsQuery->withCount(['views', 'likes', 'dislikes'])->simplePaginate();

        return EventResource::collection($events);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Calendar  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Calendar $event)
    {
        $event->incrementViews();
        $event->loadCount(['likes', 'dislikes']);

        return new EventResource($event);
    }

    /**
     * Interact with the event (like or dislike)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Calendar  $event
     * @return \Illuminate\Http\Response
     */
    public function interact(Request $request, Calendar $event)
    {
        $liked = $request->input('liked');

        $event->interactions()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['liked' => $liked]
        );

        return new EventResource($event->refresh());
    }

    /**
     * Get the version title of the latest version event.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestVersion()
    {
        $versionTitle = Calendar::versionEvents()->latest('starts_at')->value('version_title');

        return response()->json([
            'data' => [
                'version_title' => $versionTitle
            ]
        ]);
    }
}
