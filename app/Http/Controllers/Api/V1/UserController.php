<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Http\Resources\WalletResource;
use App\Models\Level\Level;
use App\Models\ProfileLimitation;
use Illuminate\Http\Request;
use App\Http\Resources\ProfileLimitationResource;

class UserController extends Controller
{
    public function __construct(
        private UserRepository $userRepository
    ) {
        //
    }

    /**
     * Get the list of users.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $users = User::whereNot('code', 'hm-2000000')
            ->select('id', 'name', 'code', 'score', 'email_verified_at')
            ->when(request()->has('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%');
            })
            ->when(request()->has('order-by'), function ($query) {
                $orderBy = request('order-by');
                if ($orderBy === 'score') {
                    $query->orderBy('score', 'desc');
                } elseif ($orderBy === 'registered_at_asc') {
                    $query->orderBy('email_verified_at', 'asc');
                } elseif ($orderBy === 'registered_at_desc') {
                    $query->orderBy('email_verified_at', 'desc');
                }
            })
            ->with('level', 'latestProfilePhoto', 'kyc:id,user_id,fname,lname')
            ->orderBy('score', 'desc')
            ->simplePaginate(20);

        return UserResource::collection($users);
    }

    /**
     * Get the list of top 10 users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topUsers()
    {
        return response()->json(['data' => $this->userRepository->topTenUsers()]);
    }

    /**
     * Get the wallet resource for a specific user.
     *
     * @param User $user The user object.
     * @return WalletResource
     */
    public function getWallet(User $user)
    {
        return new WalletResource($user->wallet);
    }

    /**
     * Get the count of features for a specific user.
     *
     * @param User $user The user object.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeaturesCount(User $user)
    {
        $user->loadCount([
            'features as maskoni_features_count' => function ($query) {
                $query->whereHas('properties', function ($query) {
                    $query->where('karbari', 'm');
                });
            },
            'features as tejari_features_count' => function ($query) {
                $query->whereHas('properties', function ($query) {
                    $query->where('karbari', 't');
                });
            },
            'features as amoozeshi_features_count' => function ($query) {
                $query->whereHas('properties', function ($query) {
                    $query->where('karbari', 'a');
                });
            }
        ]);

        return response()->json([
            'data' => [
                'maskoni_features_count' => $user->maskoni_features_count,
                'tejari_features_count' => $user->tejari_features_count,
                'amoozeshi_features_count' => $user->amoozeshi_features_count
            ]
        ]);
    }

    /**
     * Get the profile of a user.
     *
     * @param User $user The user object.
     * @return ProfileResource The profile resource.
     */
    public function getProfile(User $user)
    {
        $profileLimitation = ProfileLimitation::where('limiter_user_id', auth()->id())
            ->where('limited_user_id', $user->id)
            ->first();

        $user->load(['profilePhotos', 'settings:id,user_id,privacy', 'kyc' => function ($query) {
            $query->where('status', 1)->select('id', 'user_id', 'fname', 'lname');
        }])->loadCount(['followers', 'following']);

        return new ProfileResource($user);
    }

    /**
     * Get the level of a user.
     *
     * @param User $user The user object.
     * @return JsonResponse The JSON response containing the user's level information.
     */
    public function getLevel(User $user)
    {
        $user->load('level.image');

        if (is_null($user->level)) {
            return response()->json([
                'data' => [
                    'current_level' => null,
                    'previous_levels' => []
                ]
            ]);
        }

        $previousLevels = Level::where('score', '<', $user->level->score)->orderBy('score')->get();
        $currentLevel = $user->level;

        return response()->json([
            'data' => [
                'current_level' => [
                    'id' => $currentLevel->id,
                    'name' => $currentLevel->name,
                    'score' => $currentLevel->score,
                    'slug' => $currentLevel->slug,
                    'image' => config('app.admin_panel_url') . '/uploads/' . optional($currentLevel->image)->url,
                ],
                'previous_levels' => $previousLevels->map(function ($level) {
                    return [
                        'id' => $level->id,
                        'name' => $level->name,
                        'score' => $level->score,
                        'slug' => $level->slug,
                        'image' => config('app.admin_panel_url') . '/uploads/' . optional($level->image)->url
                    ];
                }),
                'score_percentage_to_next_level' => $currentLevel->getScorePercentageToNextLevel($user),
            ]
        ]);
    }

    /**
     * Get the profile limitations for a specific user.
     *
     * @param User $user The user for whom to retrieve the profile limitations.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the profile limitations.
     */
    public function getProfileLimitations(User $user)
    {
        if ($user->id === auth()->id()) {
            $profileLimitation = ProfileLimitation::where('limited_user_id', $user->id)
                ->where('limiter_user_id', auth()->id())
                ->first();
        } else {
            $profileLimitation = ProfileLimitation::where('limiter_user_id', auth()->id())
                ->where('limited_user_id', $user->id)
                ->orWhere('limiter_user_id', $user->id)
                ->where('limited_user_id', auth()->id())
                ->first();
        }

        return $profileLimitation ?
            new ProfileLimitationResource($profileLimitation)
            : response()->json(null, 204);
    }
}
