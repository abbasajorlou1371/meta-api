<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return NotificationResource::collection(request()->user()->unreadNotifications);
    }

    public function show(Request $request, Notification $notification) {
        $notification->markAsRead();
        return response()->noContent();
    }
}
