<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowResource;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FollowController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function followers(): AnonymousResourceCollection
    {
        return FollowResource::collection(auth()->user()->followers);
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function followings(): AnonymousResourceCollection
    {
        return FollowResource::collection(auth()->user()->following);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function follow(User $user, Request $request): JsonResponse
    {
        // $request->user->following()->attach($user);
        Follow::create([
            'follower_id' => $request->user()->id,
            'following_id' => $user->id,
        ]);
        $user->followed();
        return response()->json([
            'success' => 'کاربر مورد نظر فالو شد'
        ]);
    }


    /**
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function unfollow(User $user, Request $request): JsonResponse
    {
        // $request->user->following()->detach($user);
        Follow::where('follower_id', $request->user()->id)
        ->where('following_id', $user->id)
        ->delete();
        return response()->json([
            'success' => 'کاربر مورد نظر آنفالو شد'
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function remove(User $user, Request $request): JsonResponse
    {
        $request->user->followers()->detach($user);
        return response()->json([
            'success' => 'کاربر مورد نظر از لیست فالورها حذف شد'
        ]);
    }
}
