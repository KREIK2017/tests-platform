<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    public function index(Question $question): JsonResponse
    {
        $this->authorize('view', $question);

        $answers = $question->answers()->orderBy('id')->get();

        return AnswerResource::collection($answers)->response();
    }

    public function store(Request $request, Question $question): JsonResponse
    {
        $this->authorize('create', [Answer::class, $question]);

        $data = $request->validate([
            'text' => ['required', 'string', 'max:255'],
            'is_correct' => ['sometimes', 'boolean'],
        ]);

        $isCorrect = (bool) ($data['is_correct'] ?? false);

        $answer = DB::transaction(function () use ($question, $data, $isCorrect) {
            if ($isCorrect) {
                $question->answers()->update(['is_correct' => false]);
            }

            return $question->answers()->create([
                'text' => $data['text'],
                'is_correct' => $isCorrect,
            ]);
        });

        return (new AnswerResource($answer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Answer $answer): AnswerResource
    {
        $this->authorize('view', $answer);

        return new AnswerResource($answer);
    }

    public function update(Request $request, Answer $answer): AnswerResource
    {
        $this->authorize('update', $answer);

        $data = $request->validate([
            'text' => ['required', 'string', 'max:255'],
            'is_correct' => ['sometimes', 'boolean'],
        ]);

        $isCorrect = (bool) ($data['is_correct'] ?? false);

        DB::transaction(function () use ($answer, $data, $isCorrect) {
            if ($isCorrect && ! $answer->is_correct) {
                Answer::where('question_id', $answer->question_id)
                    ->where('id', '!=', $answer->id)
                    ->update(['is_correct' => false]);
            }

            $answer->update([
                'text' => $data['text'],
                'is_correct' => $isCorrect,
            ]);
        });

        return new AnswerResource($answer->fresh());
    }

    public function destroy(Answer $answer): Response
    {
        $this->authorize('delete', $answer);

        $answer->delete();

        return response()->noContent();
    }
}
