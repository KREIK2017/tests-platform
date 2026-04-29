<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TestTakingController extends Controller
{
    public function index(Request $request): View
    {
        $tests = Test::query()
            ->where('is_published', true)
            ->with('user')
            ->withCount('questions')
            ->latest()
            ->paginate(12);

        return view('tests.index', compact('tests'));
    }

    public function show(Test $test, Request $request): View
    {
        abort_unless($test->is_published, 404);

        $test->loadCount('questions');

        $user = $request->user();
        $inProgress = $user->attempts()
            ->where('test_id', $test->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        $lastCompleted = $user->attempts()
            ->where('test_id', $test->id)
            ->whereNotNull('completed_at')
            ->latest()
            ->first();

        return view('tests.show', compact('test', 'inProgress', 'lastCompleted'));
    }

    public function start(Test $test, Request $request): RedirectResponse
    {
        abort_unless($test->is_published, 404);

        $user = $request->user();

        $existing = $user->attempts()
            ->where('test_id', $test->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if ($existing) {
            return redirect()->route('attempts.take', $existing);
        }

        $totalQuestions = $test->questions()->count();
        if ($totalQuestions === 0) {
            return redirect()
                ->route('tests.show', $test)
                ->with('error', __('tests.messages.no_questions'));
        }

        $attempt = $user->attempts()->create([
            'test_id' => $test->id,
            'score' => 0,
            'total_questions' => $totalQuestions,
        ]);

        return redirect()->route('attempts.take', $attempt);
    }

    public function take(Attempt $attempt, Request $request): View|RedirectResponse
    {
        $this->authorize('take', $attempt);

        if ($attempt->completed_at !== null) {
            return redirect()->route('attempts.show', $attempt);
        }

        $attempt->load([
            'test',
            'test.questions' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'test.questions.answers' => fn ($q) => $q->orderBy('id'),
        ]);

        return view('attempts.take', compact('attempt'));
    }

    public function finish(Request $request, Attempt $attempt): RedirectResponse
    {
        $this->authorize('take', $attempt);

        if ($attempt->completed_at !== null) {
            return redirect()->route('attempts.show', $attempt);
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

        return redirect()
            ->route('attempts.show', $attempt)
            ->with('success', __('tests.messages.attempt_finished', [
                'score' => $score,
                'total' => $attempt->total_questions,
            ]));
    }

    public function myAttempts(Request $request): View
    {
        $user = $request->user();

        $query = Attempt::query()
            ->with(['test', 'user'])
            ->latest();

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $attempts = $query->paginate(15);

        return view('attempts.index', compact('attempts'));
    }

    public function showAttempt(Attempt $attempt): View
    {
        $this->authorize('view', $attempt);

        $attempt->load([
            'user',
            'test',
            'test.questions' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'test.questions.answers' => fn ($q) => $q->orderBy('id'),
            'attemptAnswers',
        ]);

        return view('attempts.show', compact('attempt'));
    }
}
