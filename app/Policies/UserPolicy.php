<?php

namespace App\Policies;

use App\Constants\FamilyMembersType;
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
        $dynasty = $user->dynasty;

        if (!$dynasty) {
            abort(403, 'شما برای اضافه کردن عضو به سلسله ابتدا میبایست سلسه خود را تاسیس کنید');
        }

        $family = $dynasty->family;

        if (FamilyMember::where('user_id', $user_to_add->id)->whereNot('family_id', $family->id)->exists()) {
            abort(403, 'این فرد قبلا عضو سلسله دیگری شده است');
        }

        $members = $family->familyMembers;

        if ($members->count() >= 11) {
            abort(403, 'تعداد مجاز عضوگیری این خانواده تکمیل شده است');
        }

        $members->each(function ($member) use ($relationship, $members) {

            if ($relationship === FamilyMembersType::FATHER && $member->relationship === $relationship) {
                abort(403, 'شما پدر خود را قبلا به سلسله خود اضافه کرده اید');
            }

            if ($relationship === FamilyMembersType::MOTHER && $member->relationship === $relationship) {
                abort(403, 'شما مادر خود را قبلا به سلسله خود اضافه کرده اید');
            }

            if ($relationship === FamilyMembersType::HUSBAND && $member->relationship === $relationship) {
                abort(403, 'شما شوهر خود را قبلا به سلسله خود اضافه کرده اید');
            }

            if ($relationship === FamilyMembersType::WIFE && $member->relationship === $relationship) {
                abort(403, 'شما زن خود را قبلا به سلسله خود اضافه کرده اید');
            }

            if ($relationship === FamilyMembersType::BROTHER || $relationship === FamilyMembersType::SISTER) {
                $sisters = $members->where('relationship', 'brother')->count();
                $brothers = $members->where('relationship', 'brother')->count();

                if (array_sum([$sisters, $brothers]) >= 4) {
                    abort(403, 'شما تعداد حد مجاز اضافه کردن برادر و خواهر خود را به سلسله پر کرده اید');
                }
            }

            if ($relationship === FamilyMembersType::OFFSPRING) {
                if ($members->where('relationship', 'offspring')->count() >= 4) {
                    abort(403, 'شما تعداد حد مجاز اضافه کردن فرزند خود را به سلسله پر کرده اید');
                }
            }
        });

        return true;
    }

    public function follow(User $user, User $user_to_follow)
    {
        return $user->id === $user_to_follow->id
            || $user->following()->firstWhere('following_id', $user_to_follow->id)
            ? abort(401, 'عملیات با خطا مواجه شد')
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
}
