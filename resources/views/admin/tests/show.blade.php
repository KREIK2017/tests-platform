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
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h2 class="h5 mb-0">
                    <i class="bi bi-question-circle me-2"></i>{{ __('tests.admin.questions_block') }}
                    <span class="badge text-bg-light ms-1">{{ $test->questions->count() }}</span>
                </h2>
                @can('create', [App\Models\Question::class, $test])
                    <a href="{{ route('admin.tests.questions.create', $test) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('tests.actions.add_question') }}
                    </a>
                @endcan
            </div>

            @if ($test->questions->isEmpty())
                <p class="text-muted text-center py-4 mb-0">
                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                    {{ __('tests.messages.no_questions') }}
                </p>
            @else
                <div class="accordion" id="questionsAccordion">
                    @foreach ($test->questions as $i => $question)
                        @php($collapseId = 'qBody-' . $question->id)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="qHead-{{ $question->id }}">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                        aria-expanded="false" aria-controls="{{ $collapseId }}">
                                    <span class="badge text-bg-secondary me-2">{{ $i + 1 }}</span>
                                    <span class="fw-semibold">{{ $question->text }}</span>
                                </button>
                            </h2>
                            <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                 aria-labelledby="qHead-{{ $question->id }}" data-bs-parent="#questionsAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex justify-content-end gap-2 mb-3">
                                        @can('update', $question)
                                            <a href="{{ route('admin.questions.edit', $question) }}"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil me-1"></i>{{ __('messages.common.edit') }}
                                            </a>
                                        @endcan
                                        @can('delete', $question)
                                            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}"
                                                  onsubmit="return confirm('{{ __('tests.admin.confirm_delete_question') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash me-1"></i>{{ __('messages.common.delete') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </div>

                                    <h3 class="h6 text-uppercase text-muted">{{ __('tests.admin.answers_block') }}</h3>

                                    @if ($question->answers->isEmpty())
                                        <p class="text-muted small mb-0">{{ __('messages.common.empty') }}</p>
                                    @else
                                        <ul class="list-group list-group-flush">
                                            @foreach ($question->answers as $answer)
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span>
                                                        @if ($answer->is_correct)
                                                            <i class="bi bi-check-circle-fill text-success me-2"
                                                               title="{{ __('tests.answers.fields.is_correct') }}"></i>
                                                        @else
                                                            <i class="bi bi-circle text-muted me-2"></i>
                                                        @endif
                                                        {{ $answer->text }}
                                                    </span>
                                                    <span class="d-flex gap-1">
                                                        @can('update', $answer)
                                                            <a href="{{ route('admin.answers.edit', $answer) }}"
                                                               class="btn btn-sm btn-link link-secondary p-1"
                                                               title="{{ __('messages.common.edit') }}">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @endcan
                                                        @can('delete', $answer)
                                                            <form method="POST"
                                                                  action="{{ route('admin.answers.destroy', $answer) }}"
                                                                  onsubmit="return confirm('{{ __('tests.admin.confirm_delete_answer') }}');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="btn btn-sm btn-link link-danger p-1"
                                                                        title="{{ __('messages.common.delete') }}">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @can('create', [App\Models\Answer::class, $question])
                                        <a href="{{ route('admin.questions.answers.create', $question) }}"
                                           class="btn btn-sm btn-outline-primary mt-3">
                                            <i class="bi bi-plus-circle me-1"></i>{{ __('tests.admin.add_answer') }}
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
