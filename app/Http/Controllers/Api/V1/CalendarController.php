<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Http\Resources\EventResource;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Display a listing of the events.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'event');
        $search = $request->query('search', '');
        $eventsQuery = Calendar::query();

        $eventsQuery = match ($type) {
            'version' => $eventsQuery->versions(),
            'event' => $eventsQuery->events(),
        };

        if ($search) {
            $eventsQuery->where('title', 'like', '%' . $search . '%');
        }

        $events = $eventsQuery->withCount(['views', 'likes', 'dislikes']);

        // Load user interaction if user is authenticated
        if (Auth::check()) {
            $events->with('userInteraction');
        }

        $events = $events->latest()->simplePaginate();

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
        $event->loadCount(['likes', 'dislikes', 'views']);

        // Load user interaction if user is authenticated
        // if (Auth::check()) {
            $event->load('userInteraction');
        // }

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

        $event->loadCount(['likes', 'dislikes']);
        $event->load('userInteraction');

        return new EventResource($event);
    }

    /**
     * Get the version title of the latest version event.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestVersion()
    {
        $versionTitle = Calendar::versions()->latest('starts_at')->value('version_title');

        return response()->json([
            'data' => [
                'version_title' => $versionTitle
            ]
        ]);
    }

    /**
     * Filter events based on a date range.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function filterByDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = jalali_to_carbon($request->query('start_date'));
        $endDate = jalali_to_carbon($request->query('end_date'));

        $events = Calendar::whereBetween('starts_at', [$startDate, $endDate])
            ->events()
            ->latest()
            ->get();

        $events = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'starts_at' => jdate($event->starts_at)->format('Y/m/d'),
                'color' => $event->color,
            ];
        });

        return response()->json([
            'data' => $events
        ]);
    }
}
