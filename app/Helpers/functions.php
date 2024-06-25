<?php

use App\Models\Challenge\Question;
use App\Models\Challenge\UserQuestionAnswer;
use App\Models\Feature\FeatureHourlyProfit;
use App\Models\Level\Level;
use App\Models\User;

function convertShamsiToGregorian($date): string
{
    $date = \Morilog\Jalali\CalendarUtils::convertNumbers($date, true);
    $date = str_replace('/', '-', $date);
    return \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y-m-d', $date)
        ->format('Y-m-d');
}

function getUnansweredQuestionsCount(User $user): int
{
    $answeredQuestions = UserQuestionAnswer::whereUserId($user->id)->select(['id'])->get();
    return Question::whereNotIn('id', $answeredQuestions)->count();
}

function getRelationshipTitle(string $relationsip)
{
    return match ($relationsip) {
        'brother' => 'برادر',
        'sister' => 'خواهر',
        'offspring' => 'فرزند',
        'father' => 'پدر',
        'mother' => 'مادر',
        'husband' => 'شوهر',
        'wife' => 'زن',
    };
}

function getScorePercentageToNextLevel(?Level $level, int $score): int
{
    if (!$level) {
        if ($score == 0) return 0;

        $firstLevel = Level::first();
        return ($score / $firstLevel->score) * 100;
    } else {
        $nextLevel = Level::find($level->id + 1);
        if (is_null($nextLevel)) return 0;
        return ($score / $nextLevel->score) * 100;
    }
}

function hourlyProfitInfo(User $user): int
{
    $profit = FeatureHourlyProfit::whereUserId($user->id)->oldest('dead_line')->first();
    $userDeadLine = $user->variables->withdraw_profit;

    if (is_null($profit)) {
        return 0;
    }

    $daysDiff = $profit->dead_line->diffInDays(now());
    $remainingPercentage = ((int)$userDeadLine - $daysDiff) / $userDeadLine * 100;

    return ($daysDiff > $userDeadLine) ? 100 : $remainingPercentage;
}


function getSubLevels($userLevel): array
{
    return $userLevel ? Level::where('score', '<', $userLevel->score)->orderBy('score')
        ->get()->map(function ($level) {
            return [
                'id' => $level->id,
                'name' => $level->name,
                'slug' => $level->slug,
                'score' => $level->score,
                'image' => config('app.admin_panel_url') . '/uploads/' . $level->image?->url,
            ];
        })->toArray() : [];
}
