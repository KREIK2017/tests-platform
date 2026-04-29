<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionController extends Controller
{
    public function index(Test $test): JsonResponse
    {
        $this->authorize('view', $test);

        $questions = $test->questions()
            ->with('answers')
            ->orderBy('order')
            ->orderBy('id')
            ->paginate(20);

        return QuestionResource::collection($questions)->response();
    }

    public function store(Request $request, Test $test): JsonResponse
    {
        $this->authorize('create', [Question::class, $test]);

        $data = $request->validate([
            'text' => ['required', 'string', 'min:5', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'answers' => ['required', 'array', 'min:2'],
            'answers.*.text' => ['required', 'string', 'max:255'],
            'correct_answer' => ['required', 'integer', 'min:0'],
        ]);

        $question = \Illuminate\Support\Facades\DB::transaction(function () use ($test, $data) {
            $question = $test->questions()->create([
                'text' => $data['text'],
                'order' => $data['order'] ?? ($test->questions()->max('order') + 1),
            ]);

            $correctIndex = (int) $data['correct_answer'];
            foreach ($data['answers'] as $i => $answer) {
                $question->answers()->create([
                    'text' => $answer['text'],
                    'is_correct' => $i === $correctIndex,
                ]);
            }

            return $question;
        });

        return (new QuestionResource($question->load('answers')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Question $question): QuestionResource
    {
        $this->authorize('view', $question);

        return new QuestionResource($question->load('answers'));
    }

    public function update(Request $request, Question $question): QuestionResource
    {
        $this->authorize('update', $question);

        $data = $request->validate([
            'text' => ['required', 'string', 'min:5', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'answers' => ['nullable', 'array', 'min:2'],
            'answers.*.id' => ['nullable', 'integer', 'exists:answers,id'],
            'answers.*.text' => ['required', 'string', 'max:255'],
            'correct_answer' => ['nullable', 'integer', 'min:0'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($question, $data) {
            $question->update([
                'text' => $data['text'],
                'order' => $data['order'] ?? $question->order,
            ]);

            if (isset($data['answers'])) {
                $correctIndex = (int) ($data['correct_answer'] ?? 0);
                
                // For simplicity in this lab, we can delete and recreate or sync
                // Let's do a simple sync-like approach:
                $existingIds = [];
                foreach ($data['answers'] as $i => $answerData) {
                    if (isset($answerData['id'])) {
                        $answer = $question->answers()->find($answerData['id']);
                        if ($answer) {
                            $answer->update([
                                'text' => $answerData['text'],
                                'is_correct' => $i === $correctIndex,
                            ]);
                            $existingIds[] = $answer->id;
                            continue;
                        }
                    }
                    
                    $newAnswer = $question->answers()->create([
                        'text' => $answerData['text'],
                        'is_correct' => $i === $correctIndex,
                    ]);
                    $existingIds[] = $newAnswer->id;
                }
                
                // Delete answers that were not in the request
                $question->answers()->whereNotIn('id', $existingIds)->delete();
            }
        });

        return new QuestionResource($question->fresh('answers'));
    }

    public function destroy(Question $question): Response
    {
        $this->authorize('delete', $question);

        $question->delete();

        return response()->noContent();
    }
}
