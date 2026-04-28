@extends('layouts.app')

@section('title', __('tests.admin.index_title'))

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1">{{ __('tests.admin.index_title') }}</h1>
            <p class="text-muted mb-0">{{ __('tests.admin.index_lead') }}</p>
        </div>
        @can('create', App\Models\Test::class)
            <a href="{{ route('admin.tests.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>{{ __('tests.actions.create') }}
            </a>
        @endcan
    </div>

    @if ($tests->isEmpty())
        <div class="alert alert-light border text-center py-5">
            <i class="bi bi-inbox display-5 d-block mb-2 text-muted"></i>
            <p class="mb-3">{{ __('tests.messages.no_tests') }}</p>
            @can('create', App\Models\Test::class)
                <a href="{{ route('admin.tests.create') }}" class="btn btn-primary">
                    {{ __('tests.actions.create') }}
                </a>
            @endcan
        </div>
    @else
        <div class="row g-4">
            @foreach ($tests as $test)
                <div class="col-md-6 col-lg-4">
                    <div class="card test-card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $test->title }}</h5>
                                @if ($test->is_published)
                                    <span class="badge text-bg-success">{{ __('tests.status.published') }}</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ __('tests.status.draft') }}</span>
                                @endif
                            </div>

                            <p class="card-text text-muted small mb-2">
                                <i class="bi bi-person me-1"></i>{{ $test->user->name }}
                                <span class="mx-1">•</span>
                                <i class="bi bi-question-circle me-1"></i>{{ $test->questions_count }}
                            </p>

                            @if ($test->description)
                                <p class="card-text small text-truncate-2">{{ $test->description }}</p>
                            @endif

                            <div class="mt-auto pt-3 d-flex flex-wrap gap-2">
                                @can('view', $test)
                                    <a href="{{ route('admin.tests.show', $test) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>{{ __('tests.actions.view') }}
                                    </a>
                                @endcan
                                @can('update', $test)
                                    <a href="{{ route('admin.tests.edit', $test) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil me-1"></i>{{ __('messages.common.edit') }}
                                    </a>
                                @endcan
                                @can('delete', $test)
                                    <form method="POST" action="{{ route('admin.tests.destroy', $test) }}"
                                          onsubmit="return confirm('{{ __('tests.messages.confirm_delete', ['title' => $test->title]) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>{{ __('messages.common.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $tests->links() }}
        </div>
    @endif
@endsection
