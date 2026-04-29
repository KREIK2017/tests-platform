<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestResource;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Test::query()
            ->with('user')
            ->withCount('questions')
            ->latest();

        if (! $user->isAdmin()) {
            $query->where('is_published', true);
        }

        $tests = $query->paginate($request->integer('per_page', 12));

        return TestResource::collection($tests)->response();
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Test::class);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['sometimes', 'boolean'],
        ]);

        $test = Test::create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return (new TestResource($test->load('user')->loadCount('questions')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Test $test): TestResource
    {
        $this->authorize('view', $test);

        $test->load([
            'user',
            'questions' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'questions.answers' => fn ($q) => $q->orderBy('id'),
        ])->loadCount('questions');

        return new TestResource($test);
    }

    public function update(Request $request, Test $test): TestResource
    {
        $this->authorize('update', $test);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['sometimes', 'boolean'],
        ]);

        $test->update($data);

        return new TestResource($test->fresh()->load('user')->loadCount('questions'));
    }

    public function destroy(Test $test): Response
    {
        $this->authorize('delete', $test);

        $test->delete();

        return response()->noContent();
    }
}
