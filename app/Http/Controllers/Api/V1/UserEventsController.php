<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportEventRequest;
use App\Http\Resources\UserEventReportResource;
use App\Http\Resources\UserEventReportResponseResource;
use App\Http\Resources\UserEventResource;
use App\Models\User\UserEvent;
use Illuminate\Http\Request;

class UserEventsController extends Controller
{
    /**
     * Get a paginated list of user events.
     *
     * @return \App\Http\Resources\UserEventResourceCollection
     */
    public function index()
    {
        return UserEventResource::collection(
            UserEvent::whereBelongsto(request()->user())->simplePaginate(10)
        );
    }

    /**
     * Get the details of a user event.
     *
     * @param UserEvent $userEvent
     * @return \App\Http\Resources\UserEventResource
     */
    public function show(UserEvent $userEvent)
    {
        return new UserEventResource($userEvent);
    }

    /**
     * Store a report for a user event.
     *
     * @param ReportEventRequest $request
     * @param UserEvent $userEvent
     * @return \App\Http\Resources\UserEventReportResource
     */
    public function store(ReportEventRequest $request, UserEvent $userEvent)
    {
        $report = $userEvent->report()->create([
            'suspecious_citizen' => $request->suspecious_citizen,
            'event_description' => $request->event_description
        ]);

        return new UserEventReportResource($report);
    }

    /**
     * Send a response to a user event report.
     *
     * @param Request $request
     * @param UserEvent $userEvent
     * @return \App\Http\Resources\UserEventReportResponseResource
     */
    public function sendResponse(Request $request, UserEvent $userEvent)
    {
        $request->validate(['response' => 'required|string|max:300']);

        $response = $userEvent->report->responses()->create([
            'responser_name' => $request->user()->name,
            'response' => $request->response,
        ]);

        $userEvent->report->update(['status' => 1]);
        return new UserEventReportResponseResource($response);
    }

    /**
     * Close a user event report.
     *
     * @param UserEvent $userEvent
     * @return \Illuminate\Http\Response
     */
    public function closeEventReport(UserEvent $userEvent)
    {
        $userEvent->report->update(['closed' => 1]);
        return response()->noContent();
    }
}
