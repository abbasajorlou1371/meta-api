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
    public function index()
    {
        return UserEventResource::collection(
            UserEvent::whereBelongsto(request()->user())->simplePaginate(10)
        );
    }

    public function show(UserEvent $userEvent)
    {
        return new UserEventResource($userEvent);
    }

    public function store(ReportEventRequest $request, UserEvent $userEvent)
    {
        $report = $userEvent->report()->create([
            'suspecious_citizen' => $request->suspecious_citizen,
            'event_description' => $request->event_description
        ]);

        return new UserEventReportResource($report);
    }

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

    public function closeEventReport(UserEvent $userEvent)
    {
        $userEvent->report->update(['closed' => 1]);
        return response()->noContent();
    }
}
