<?php

namespace App\Policies;

use App\Models\Challenge\Question;
use App\Models\Challenge\UserQuestionAnswer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;

    public function answer(User $user, Question $question): bool
    {
        return UserQuestionAnswer::whereUserId($user->id)
            ->whereQuestionId($question->id)
            ->whereAnswerId($question->correctAnswer->id)
            ->doesntExist();
    }
}
