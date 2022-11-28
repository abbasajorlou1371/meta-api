<?php

namespace App\Policies;

use App\Constants\FamilyMembersType;
use App\Constants\JoinRequestStatus;
use App\Models\Dynasty\FamilyMember;
use App\Models\User;
use App\Models\User\Custom;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can add new member.
     *
     * @param User $user
     * @param User $user_to_add
     * @param string $relationship
     * @return Response|bool
     */
    public function addFamilyMember(User $user, User $user_to_add, string $relationship)
    {
        if (!$user_to_add->verified()) return false;
        if ($user->id == $user_to_add->id) return false;
        if (
            DB::table('join_requests')
            ->where('from_user', $user->id)
            ->where('to_user', $user_to_add->id)
            ->where('status', JoinRequestStatus::REJECTED)
            ->exists()
        ) return false;
        $dynasty = $user->dynasty;

        if (!$dynasty) return false;

        $family = $dynasty->family;

        if (FamilyMember::where('user_id', $user_to_add->id)->whereNot('family_id', $family->id)->exists()) return false;

        $members = $family->familyMembers;

        if ($members->count() >= 11) return false;

        $members->each(function ($member) use ($relationship, $members) {

            if ($relationship === FamilyMembersType::FATHER && $member->relationship === $relationship) return false;

            if ($relationship === FamilyMembersType::MOTHER && $member->relationship === $relationship) return false;

            if ($relationship === FamilyMembersType::HUSBAND && $member->relationship === $relationship) return false;

            if ($relationship === FamilyMembersType::WIFE && $member->relationship === $relationship) return false;
            if ($relationship === FamilyMembersType::BROTHER || $relationship === FamilyMembersType::SISTER) {
                $sisters = $members->where('relationship', 'brother')->count();
                $brothers = $members->where('relationship', 'brother')->count();

                if (array_sum([$sisters, $brothers]) >= 4) return false;
            }

            if ($relationship === FamilyMembersType::OFFSPRING) {
                if ($members->where('relationship', 'offspring')->count() >= 4) return false;
            }
        });

        return true;
    }

    public function follow(User $user, User $user_to_follow)
    {
        return $user->id === $user_to_follow->id
            || $user->following()->firstWhere('following_id', $user_to_follow->id)
            ? false
            : true;
    }

    public function addCustom(User $user)
    {
        return is_null($user->customs);
    }

    public function updateCustom(User $user, Custom $custom)
    {
        return $user->id == $custom->user_id && $user->customs->updated_at < now()->subMonth();
    }

    public function changePermissions(User $user, User $child)
    {
        if (!isUnderEighteen($child)) return false;
        $dynasty = $user->dynasty;

        $family = $dynasty->family;
        $familyMembers = $family->familyMembers;

        if (!$familyMembers->where('user_id', $child->id)->first()) return false;
        return true;
    }
}
