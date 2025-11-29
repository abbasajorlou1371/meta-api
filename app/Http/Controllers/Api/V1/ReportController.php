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
        $reports = Report::whereBelongsTo(request()->user())
            ->select('id', 'user_id', 'title', 'subject', 'status', 'created_at')
            ->latest()
            ->simplePaginate(10);

        return ReportResource::collection($reports);
    }

    /**
     * Display the specified resource.
     *
     * @param Report $report
     * @return JsonResponse
     */
    public function show(Report $report)
    {
        $report->load('images');

        return new ReportResource($report);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ReportRequest $request
     * @return JsonResponse
     */
    public function store(ReportRequest $request)
    {
        $report = $request->user()->reports()->create($request->only([
            'subject',
            'title',
            'content',
            'url'
        ]));

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedPath = $file->store('reports', 'public');

                // Remove execution permissions from uploaded file (security measure)
                $fullPath = storage_path('app/public/' . $storedPath);
                if (file_exists($fullPath)) {
                    chmod($fullPath, 0644);
                }

                $report->images()->create(['url' => $storedPath]);
            }
        }

        return new ReportResource($report);
    }
}
