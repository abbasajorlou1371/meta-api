<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return ReportResource::collection(request()->user()->reports);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ReportRequest $request
     * @return JsonResponse
     */
    public function store(ReportRequest $request)
    {
        $report = $request->user()->reports()->create([
            'subject' => $request->subject,
            'title'   => $request->title,
            'content' => $request->content,
            'url'     => $request->url
        ]);
        return new ReportResource($report);
    }

    /**
     * Display the specified resource.
     *
     * @param Report $report
     * @return JsonResponse
     */
    public function show(Report $report)
    {
        return new ReportResource($report);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param ReportRequest $request
     * @param Report $report
     * @return JsonResponse
     */
    public function update(ReportRequest $request, Report $report)
    {
        $report->update([
            'subject' => $request->subject,
            'title'   => $request->title,
            'content' => $request->content,
            'url'     => $request->url
        ]);
        return new ReportResource($report->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Report $report
     * @return JsonResponse
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return response()->noContent();
    }
}
