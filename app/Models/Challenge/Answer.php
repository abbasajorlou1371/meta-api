<?php

namespace App\Models\Challenge;

use App\Models\Challenge\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Answer extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => config('rgb.ftp-endpoint').'public/challenge/' . $value . '.png'
        );
    }

    public function isCorrect()
    {
        return $this->is_correct;
    }

    public function answerPercentage()
    {
        $selectedAnswer = UserQuestionAnswer::where('answer_id', $this->id)->where('question_id', $this->question->id)->count();
        $totalAnswered = UserQuestionAnswer::where('question_id', $this->question->id)->count();
        return $totalAnswered > 0 ? floor($selectedAnswer / $totalAnswered * 100) : 0;
    }
}
