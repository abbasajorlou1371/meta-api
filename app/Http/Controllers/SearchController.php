<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeatureResource;
use App\Http\Resources\UserResource;
use App\Models\FeatureProperties;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SearchController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function users(Request $request): AnonymousResourceCollection
    {
        $users = User::where('name', 'like', '%' . $request->searchTerm . '%')
        ->orWhere('code', 'like', '%' . $request->searchTerm . '%')
        ->lazy();
        return UserResource::collection($users);
    }

    /**
     * @param Request $request
     * @return Response|JsonResponse|Application|ResponseFactory
     */
    public function features(Request $request): Response|JsonResponse|Application|ResponseFactory
    {
        $feature_properties = FeatureProperties::where('id', 'like', '%' . $request->searchTerm . '%')
        ->orWhere('address', 'like', '%' . $request->searchTerm . '%')
        ->lazy();
        return response()->json([
            'feature_properties' => $feature_properties
        ]);
    }
}
