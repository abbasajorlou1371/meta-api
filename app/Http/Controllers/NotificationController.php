<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get unreadNotifications of the currenct user.
     * @return NotificationResource
     */
    public function index()
    {
        return NotificationResource::collection(request()->user()->unreadNotifications);
    }

    /**
     * Get the details of a notification.
     * @param Notification $notification
     * @return NotificationResource
     */
    public function show(Request $request, Notification $notification) {
        $notification->markAsRead();
        return response()->noContent();
    }
}
