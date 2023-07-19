<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        return new NotificationResource($notification);
    }

    /**
     * Mark a notification as read.
     * @param Notification $notification
     * @return NotificationResource
     */
    public function markAsRead(Request $request, Notification $notification) {
        $notification->markAsRead();
        return response()->noContent();
    }

    /**
     * Mark all notifications as read.
     * @return NotificationResource
     */
    public function markAllAsRead(Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->noContent();
    }
}
