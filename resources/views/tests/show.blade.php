@extends('layouts.app')

@section('title', $test->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="{{ route('tests.index') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i>{{ __('tests.student.index_title') }}
            </a>

            <h1 class="h3 mb-3 mt-2">{{ $test->title }}</h1>

            @if ($test->description)
                <p class="lead text-muted">{{ $test->description }}</p>
            @endif

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">{{ __('tests.fields.author') }}</dt>
                        <dd class="col-sm-8">{{ $test->user->name }}</dd>

                        <dt class="col-sm-4 text-muted">{{ __('tests.student.questions_count') }}</dt>
                        <dd class="col-sm-8">{{ $test->questions_count }}</dd>
                    </dl>
                </div>
            </div>

            @if ($inProgress)
                <div class="alert alert-info">
                    <i class="bi bi-hourglass-split me-2"></i>
                    {{ __('tests.student.start_warning_in_progress') }}
                </div>
            @elseif ($lastCompleted)
                <div class="alert alert-secondary">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ __('tests.student.start_warning_completed') }}
                    <div class="small mt-1">
                        {{ __('tests.student.previous_score', [
                            'score' => $lastCompleted->score,
                            'total' => $lastCompleted->total_questions,
                        ]) }}
                        — <a href="{{ route('attempts.show', $lastCompleted) }}">{{ __('tests.actions.view') }}</a>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('tests.start', $test) }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg"
                        @disabled($test->questions_count === 0)>
                    <i class="bi bi-play-circle me-1"></i>{{ __('tests.actions.start') }}
                </button>
                <a href="{{ route('tests.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                    {{ __('messages.common.back') }}
                </a>
            </form>

            @if ($test->questions_count === 0)
                <p class="text-muted small mt-3">{{ __('tests.messages.no_questions') }}</p>
            @endif
        </div>
    </div>
@endsection
