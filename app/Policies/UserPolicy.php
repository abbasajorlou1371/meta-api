<?php

namespace App\Policies;

use App\Constants\FamilyMembersType;
use App\Constants\JoinRequestStatus;
use App\Models\Dynasty\FamilyMember;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can add new member.
     *
     * @param User $user
     * @param User $user_to_add
     * @param string $relationship
     * @return Response|bool
     */
    public function addFamilyMember(User $user, User $userToAdd, string $relationship)
    {
        if ($user->id == $userToAdd->id) return false;

        $dynasty = $user->dynasty;

        if (is_null($dynasty)) return false;

        $family = $dynasty->family;

        if (FamilyMember::where('user_id', $userToAdd->id)->exists()) return false;

        $members = $family->familyMembers;

        if ($members->count() >= 11) return false;

        if (!$userToAdd->verified())
        {
            return Response::deny(sprintf('کاربر %s احراز هویت نکرده است.', $userToAdd->code), 403);
        }

        $members->each(function ($member) use ($relationship, $members) {

            if ($relationship === 'father' && $member->relationship === $relationship) return false;

            if ($relationship === 'mother' && $member->relationship === $relationship) return false;

            if ($relationship === 'husband' && $member->relationship === $relationship) return false;

            if ($relationship === 'wife' && $member->relationship === $relationship) return false;

            if ($relationship === 'brother' || $relationship === 'sister') {
                $sisters = $members->where('relationship', 'brother')->count();
                $brothers = $members->where('relationship', 'brother')->count();

                if (array_sum([$sisters, $brothers]) >= 4) return false;
            }

            if ($relationship === 'offspring') {
                if ($members->where('relationship', 'offspring')->count() >= 4) return false;
            }
        });

        return true;
    }

    public function follow(User $user, User $user_to_follow)
    {
        return $user->id !== $user_to_follow->id
            && Follow::where('follower_id', $user->id)->where('following_id', $user_to_follow->id)->doesntExist();
    }

    public function controlPermissions(User $user, User $child)
    {
        if ($child->user->is($user)) return false;
        if (!$child->isUnderEighteen()) return false;
        $dynasty = $user->dynasty;
        $family = $dynasty->family;
        if (FamilyMember::where('family_id', $family->id)->where('user_id', $child->id)->doesntExist()) return false;
        return true;
    }
}
