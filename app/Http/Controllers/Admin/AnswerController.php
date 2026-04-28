<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnswerRequest;
use App\Http\Requests\Admin\UpdateAnswerRequest;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnswerController extends Controller
{
    public function create(Question $question): View
    {
        $this->authorize('create', [Answer::class, $question]);

        return view('admin.answers.create', compact('question'));
    }

    public function store(StoreAnswerRequest $request, Question $question): RedirectResponse
    {
        $this->authorize('create', [Answer::class, $question]);

        $data = $request->validated();

        DB::transaction(function () use ($question, $data) {
            if (! empty($data['is_correct'])) {
                $question->answers()->update(['is_correct' => false]);
            }

            $question->answers()->create([
                'text' => $data['text'],
                'is_correct' => (bool) ($data['is_correct'] ?? false),
            ]);
        });

        return redirect()
            ->route('admin.tests.show', $question->test_id)
            ->with('success', __('tests.messages.question_updated'));
    }

    public function edit(Answer $answer): View
    {
        $this->authorize('update', $answer);

        $answer->load('question.test');

        return view('admin.answers.edit', compact('answer'));
    }

    public function update(UpdateAnswerRequest $request, Answer $answer): RedirectResponse
    {
        $this->authorize('update', $answer);

        $data = $request->validated();

        DB::transaction(function () use ($answer, $data) {
            if (! empty($data['is_correct']) && ! $answer->is_correct) {
                Answer::where('question_id', $answer->question_id)
                    ->where('id', '!=', $answer->id)
                    ->update(['is_correct' => false]);
            }

            $answer->update([
                'text' => $data['text'],
                'is_correct' => (bool) ($data['is_correct'] ?? false),
            ]);
        });

        return redirect()
            ->route('admin.tests.show', $answer->question->test_id)
            ->with('success', __('tests.messages.question_updated'));
    }

    public function destroy(Answer $answer): RedirectResponse
    {
        $this->authorize('delete', $answer);

        $testId = $answer->question->test_id;
        $answer->delete();

        return redirect()
            ->route('admin.tests.show', $testId)
            ->with('success', __('tests.messages.question_updated'));
    }
}
