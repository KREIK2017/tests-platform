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
        ]);

        $question = $test->questions()->create([
            'text' => $data['text'],
            'order' => $data['order'] ?? ($test->questions()->max('order') + 1),
        ]);

        return (new QuestionResource($question))
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
        ]);

        $question->update([
            'text' => $data['text'],
            'order' => $data['order'] ?? $question->order,
        ]);

        return new QuestionResource($question->fresh());
    }

    public function destroy(Question $question): Response
    {
        $this->authorize('delete', $question);

        $question->delete();

        return response()->noContent();
    }
}
