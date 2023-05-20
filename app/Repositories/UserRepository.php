<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{
    public function topPlayers()
    {
        return User::where('score', '>', 0)
            ->orderByDesc('score')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'image' => $user->profilePhotos->last()?->url,
                    'online' => $user->last_seen->diffInMinutes(now()) < 2,
                    'level' => $user->level?->slug,
                    'code' => $user->code,
                ];
            });
    }
}
