<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttemptResource;
use App\Models\Attempt;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AttemptController extends Controller
{
    public function start(Request $request, Test $test): JsonResponse
    {
        if (! $test->is_published) {
            return response()->json(['message' => 'Test is not published.'], Response::HTTP_NOT_FOUND);
        }

        $user = $request->user();

        $existing = $user->attempts()
            ->where('test_id', $test->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if ($existing) {
            return (new AttemptResource($existing->load('test')))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }

        $totalQuestions = $test->questions()->count();
        if ($totalQuestions === 0) {
            return response()->json(
                ['message' => __('tests.messages.no_questions')],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $attempt = $user->attempts()->create([
            'test_id' => $test->id,
            'score' => 0,
            'total_questions' => $totalQuestions,
        ]);

        return (new AttemptResource($attempt->load('test')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function finish(Request $request, Attempt $attempt): JsonResponse
    {
        $this->authorize('take', $attempt);

        if ($attempt->completed_at !== null) {
            return (new AttemptResource(
                $attempt->load(['test.questions.answers', 'attemptAnswers'])
            ))->response();
        }

        $attempt->load(['test.questions.answers']);

        $rules = ['answers' => ['required', 'array']];
        foreach ($attempt->test->questions as $question) {
            $allowed = $question->answers->pluck('id')->implode(',');
            $rules["answers.{$question->id}"] = ['required', 'integer', "in:{$allowed}"];
        }

        $validated = $request->validate($rules);

        $score = 0;
        DB::transaction(function () use ($attempt, $validated, &$score) {
            foreach ($attempt->test->questions as $question) {
                $answerId = (int) $validated['answers'][$question->id];
                $answer = $question->answers->firstWhere('id', $answerId);

                $attempt->attemptAnswers()->create([
                    'question_id' => $question->id,
                    'answer_id' => $answer->id,
                ]);

                if ($answer->is_correct) {
                    $score++;
                }
            }

            $attempt->update([
                'score' => $score,
                'total_questions' => $attempt->test->questions->count(),
                'completed_at' => now(),
            ]);
        });

        return (new AttemptResource(
            $attempt->fresh()->load(['test.questions.answers', 'attemptAnswers'])
        ))->response();
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Attempt::query()
            ->with(['test', 'user'])
            ->latest();

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $attempts = $query->paginate($request->integer('per_page', 15));

        return AttemptResource::collection($attempts)->response();
    }

    public function show(Attempt $attempt): AttemptResource
    {
        $this->authorize('view', $attempt);

        $attempt->load([
            'user',
            'test',
            'test.questions' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'test.questions.answers' => fn ($q) => $q->orderBy('id'),
            'attemptAnswers',
        ]);

        return new AttemptResource($attempt);
    }
}
