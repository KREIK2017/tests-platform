@extends('layouts.app')

@section('title', __('tests.student.index_title'))

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">{{ __('tests.student.index_title') }}</h1>
        <p class="text-muted mb-0">{{ __('tests.student.index_lead') }}</p>
    </div>

    @if ($tests->isEmpty())
        <div class="alert alert-light border text-center py-5">
            <i class="bi bi-inbox display-5 d-block mb-2 text-muted"></i>
            <p class="mb-0">{{ __('tests.messages.no_published_tests') }}</p>
        </div>
    @else
        <div class="row g-4">
            @foreach ($tests as $test)
                <div class="col-md-6 col-lg-4">
                    <div class="card test-card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $test->title }}</h5>

                            @if ($test->description)
                                <p class="card-text small text-muted">{{ $test->description }}</p>
                            @endif

                            <p class="card-text small text-muted mb-3">
                                <i class="bi bi-question-circle me-1"></i>{{ $test->questions_count }}
                                <span class="mx-1">•</span>
                                <i class="bi bi-person me-1"></i>{{ $test->user->name }}
                            </p>

                            <a href="{{ route('tests.show', $test) }}" class="btn btn-primary mt-auto">
                                {{ __('tests.actions.view') }}
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
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
