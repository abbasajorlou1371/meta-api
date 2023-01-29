<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FollowResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FollowController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function followers(Request $request): AnonymousResourceCollection
    {
        return FollowResource::collection($request->user()->followers);
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function followings(Request $request): AnonymousResourceCollection
    {
        return FollowResource::collection($request->user()->following);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function follow(User $user, Request $request)
    {
        $this->authorize('follow', $user);
        $request->user()->following()->attach($user);
        $user->followed();
        return response()->noContent(200);
    }


    /**
     * @param User $user
     * @param Request $request
     */
    public function unfollow(User $user, Request $request)
    {
        $request->user()->following()->detach($user);
        return response()->noContent(200);
    }

    /**
     * @param User $user
     * @param Request $request
     */
    public function remove(User $user, Request $request)
    {
        $request->user()->followers()->detach($user);
        return response()->noContent(200);
    }
}
