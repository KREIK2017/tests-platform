<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Http\Requests\Admin\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function create(Test $test): View
    {
        $this->authorize('create', [Question::class, $test]);

        return view('admin.questions.create', compact('test'));
    }

    public function store(StoreQuestionRequest $request, Test $test): RedirectResponse
    {
        $this->authorize('create', [Question::class, $test]);

        $data = $request->validated();
        $correctIndex = (int) $data['correct_answer'];
        $nextOrder = $data['order'] ?? ($test->questions()->max('order') + 1);

        DB::transaction(function () use ($test, $data, $correctIndex, $nextOrder) {
            $question = $test->questions()->create([
                'text' => $data['text'],
                'order' => $nextOrder,
            ]);

            foreach ($data['answers'] as $i => $answer) {
                $question->answers()->create([
                    'text' => $answer['text'],
                    'is_correct' => $i === $correctIndex,
                ]);
            }
        });

        return redirect()
            ->route('admin.tests.show', $test)
            ->with('success', __('tests.messages.question_created'));
    }

    public function edit(Question $question): View
    {
        $this->authorize('update', $question);

        $question->load('test');

        return view('admin.questions.edit', compact('question'));
    }

    public function update(UpdateQuestionRequest $request, Question $question): RedirectResponse
    {
        $this->authorize('update', $question);

        $data = $request->validated();
        $question->update([
            'text' => $data['text'],
            'order' => $data['order'] ?? $question->order,
        ]);

        return redirect()
            ->route('admin.tests.show', $question->test_id)
            ->with('success', __('tests.messages.question_updated'));
    }

    public function destroy(Question $question): RedirectResponse
    {
        $this->authorize('delete', $question);

        $testId = $question->test_id;
        $question->delete();

        return redirect()
            ->route('admin.tests.show', $testId)
            ->with('success', __('tests.messages.question_deleted'));
    }
}
