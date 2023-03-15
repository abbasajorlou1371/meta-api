<?php

namespace App\Policies;

use App\Models\Dynasty\FamilyMember;
use App\Models\Dynasty\JoinRequest;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

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
        if ($user->isUnderEighteen()) {
            if ($user->permissions) {
                if (!$user->permissions?->verified && !$user->permissions?->DM) {
                    return false;
                }
            }
        }

        if ($user->id == $userToAdd->id) return false;

        $dynasty = $user->dynasty;

        if (is_null($dynasty)) return false;

        $family = $dynasty->family;

        $requestAlreadySent = JoinRequest::whereFromUser($user->id)
            ->whereToUser($userToAdd->id)
            ->whereStatus(0)
            ->exists();

        if ($requestAlreadySent) {
            return Response::deny('شما قبلا درخواست خود را به این کاربر ارسال کرده اید.', 403);
        }

        $rejectedByUser = JoinRequest::whereFromUser($user->id)
            ->whereToUser($userToAdd->id)
            ->whereStatus(-1)
            ->exists();

        if ($rejectedByUser) {
            return Response::deny('درخواست شما قبلا توسط این کاربر رد شده است.', 403);
        }

        if (FamilyMember::where('user_id', $userToAdd->id)->whereNot('relationship', 'owner')->exists()) return false;

        $members = $family->familyMembers;

        if ($members->count() >= 11) return false;

        if (!$userToAdd->verified()) {
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
        return $user->isNot($user_to_follow)
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

    public function buyFromStore(User $user)
    {
        return $user->isUnderEighteen()
            ? is_null($user->permissions) || $user->permissions?->verified && $user->permissions?->BFR
            : true;
    }
}
