<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTestRequest;
use App\Http\Requests\Admin\UpdateTestRequest;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Test::class);

        $tests = Test::query()
            ->with('user')
            ->withCount('questions')
            ->latest()
            ->paginate(12);

        return view('admin.tests.index', compact('tests'));
    }

    public function create(): View
    {
        $this->authorize('create', Test::class);

        return view('admin.tests.create');
    }

    public function store(StoreTestRequest $request): RedirectResponse
    {
        $this->authorize('create', Test::class);

        $test = Test::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.tests.show', $test)
            ->with('success', __('tests.messages.created'));
    }

    public function show(Test $test): View
    {
        $this->authorize('view', $test);

        $test->load([
            'user',
            'questions' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'questions.answers' => fn ($q) => $q->orderBy('id'),
        ]);

        return view('admin.tests.show', compact('test'));
    }

    public function edit(Test $test): View
    {
        $this->authorize('update', $test);

        return view('admin.tests.edit', compact('test'));
    }

    public function update(UpdateTestRequest $request, Test $test): RedirectResponse
    {
        $this->authorize('update', $test);

        $test->update($request->validated());

        return redirect()
            ->route('admin.tests.show', $test)
            ->with('success', __('tests.messages.updated'));
    }

    public function destroy(Test $test): RedirectResponse
    {
        $this->authorize('delete', $test);

        $test->delete();

        return redirect()
            ->route('admin.tests.index')
            ->with('success', __('tests.messages.deleted'));
    }
}
