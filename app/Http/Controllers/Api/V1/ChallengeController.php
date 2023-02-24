<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Challenge\Answer;
use Illuminate\Http\Request;
use App\Models\SystemVariable;
use App\Models\Challenge\Question;
use App\Models\Challenge\UserQuestionAnswer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ChallengeController extends Controller
{
    public function getTimings()
    {
        return response()->json([
            'data' => [
                'display_ad_interval' => SystemVariable::getByKey('challenge_display_ad_interval') ?? 15,
                'display_question_interval' => SystemVariable::getByKey('challenge_display_question_interval') ?? 15,
                'display_answer_interval' => SystemVariable::getByKey('challenge_display_answer_interval') ?? 15,
                // 'participants' =>
            ]
        ]);
    }

    public function getQuestion(Request $request) {
        $question = Question::with('answers')->inRandomOrder()->first();
        while(UserQuestionAnswer::where('user_id', $request->user()->id)->where('question_id', $question->id)->exists()) {
            $question = Question::with('answers')->inRandomOrder()->first();
        }
        $question->increment('views');
        return response()->json(['data' => new QuestionResource($question)]);
    }

    public function answerResult(Request $request) {
        $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'answer_id' => 'required|integer|exists:answers,id',
        ]);

        $question = Question::findOrFail($request->question_id);
        $answer = Answer::findOrFail($request->answer_id);

        if(! Gate::allows('answer-question', $question)) {
            abort(403, 'Not Allowed!');
        }

        if($answer->question->isNot($question)) {
            throw ValidationException::withMessages([
                'answer_id' => 'Answer is not valid!'
            ]);
        } else {
            UserQuestionAnswer::create([
                'user_id' => $request->user()->id,
                'question_id' => $question->id,
                'answer_id' => $answer->id,
            ]);

            $question->increment('participants');

            if($answer->isCorrect()) {
                $request->user()->assets->increment('psc', $question->prize);
            }
            return new QuestionResource($question);
        }
    }
}
