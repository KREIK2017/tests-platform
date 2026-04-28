@extends('layouts.app')

@section('title', __('tests.admin.show_title', ['title' => $test->title]))

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <a href="{{ route('admin.tests.index') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i>{{ __('tests.admin.index_title') }}
            </a>
            <h1 class="h3 mb-1 mt-1">{{ $test->title }}</h1>
            <div class="text-muted small">
                <i class="bi bi-person me-1"></i>{{ $test->user->name }}
                <span class="mx-1">•</span>
                @if ($test->is_published)
                    <span class="badge text-bg-success">{{ __('tests.status.published') }}</span>
                @else
                    <span class="badge text-bg-secondary">{{ __('tests.status.draft') }}</span>
                @endif
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @can('update', $test)
                <a href="{{ route('admin.tests.edit', $test) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i>{{ __('tests.actions.edit') }}
                </a>
            @endcan
            @can('delete', $test)
                <form method="POST" action="{{ route('admin.tests.destroy', $test) }}"
                      onsubmit="return confirm('{{ __('tests.messages.confirm_delete', ['title' => $test->title]) }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>{{ __('tests.actions.delete') }}
                    </button>
                </form>
            @endcan
        </div>
    </div>

    @if ($test->description)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-2">{{ __('tests.fields.description') }}</h2>
                <p class="mb-0">{{ $test->description }}</p>
            </div>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">
                    <i class="bi bi-question-circle me-2"></i>{{ __('tests.admin.questions_block') }}
                    <span class="badge text-bg-light ms-1">{{ $test->questions->count() }}</span>
                </h2>
                {{-- Question CRUD will land in Stage 5 --}}
                <span class="btn btn-primary disabled" aria-disabled="true">
                    <i class="bi bi-plus-circle me-1"></i>{{ __('tests.actions.add_question') }}
                </span>
            </div>

            @if ($test->questions->isEmpty())
                <p class="text-muted text-center py-4 mb-0">
                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                    {{ __('tests.messages.no_questions') }}
                </p>
            @else
                <ol class="list-group list-group-numbered list-group-flush">
                    @foreach ($test->questions as $question)
                        <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-0">
                            <div class="ms-2 me-auto">
                                <div class="fw-semibold">{{ $question->text }}</div>
                                <small class="text-muted">
                                    {{ __('tests.fields.questions_count') }}: —
                                </small>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>
@endsection
