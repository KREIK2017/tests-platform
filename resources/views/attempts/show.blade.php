@extends('layouts.app')

@section('title', __('tests.student.result_title'))

@section('content')
    @php
        $score = $attempt->score;
        $total = $attempt->total_questions;
        $percent = $total > 0 ? (int) round(100 * $score / $total) : 0;

        $resultClass = match (true) {
            $percent >= 80 => 'text-bg-success',
            $percent >= 50 => 'text-bg-warning',
            default => 'text-bg-danger',
        };

        $picked = $attempt->attemptAnswers->keyBy('question_id');
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <a href="{{ route('attempts.index') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i>{{ __('tests.student.attempts_index_title') }}
            </a>

            <h1 class="h3 mb-1 mt-2">{{ $attempt->test->title }}</h1>
            <p class="text-muted">
                <i class="bi bi-person me-1"></i>{{ $attempt->user->name }}
                @if ($attempt->completed_at)
                    <span class="mx-1">•</span>
                    <i class="bi bi-calendar-check me-1"></i>{{ $attempt->completed_at->isoFormat('LLL') }}
                @endif
            </p>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    <h2 class="h5 text-uppercase text-muted mb-3">{{ __('tests.student.result_title') }}</h2>
                    <div class="display-3 fw-bold mb-1">
                        {{ __('tests.student.result_score', ['score' => $score, 'total' => $total]) }}
                    </div>
                    <span class="badge fs-5 {{ $resultClass }}">
                        {{ __('tests.student.result_percent', ['percent' => $percent]) }}
                    </span>
                </div>
            </div>

            @foreach ($attempt->test->questions as $i => $question)
                @php
                    $pickedAnswer = $picked->get($question->id);
                    $pickedAnswerId = $pickedAnswer?->answer_id;
                    $correctAnswer = $question->answers->firstWhere('is_correct', true);
                    $isCorrect = $pickedAnswerId !== null && $pickedAnswerId === $correctAnswer?->id;
                @endphp

                <div class="card shadow-sm border-0 border-start border-4 mb-3 {{ $isCorrect ? 'border-success' : 'border-danger' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h2 class="h6 mb-0">
                                <span class="badge text-bg-secondary me-2">{{ $i + 1 }}</span>
                                {{ $question->text }}
                            </h2>
                            @if ($isCorrect)
                                <span class="badge text-bg-success">
                                    <i class="bi bi-check-circle me-1"></i>{{ __('tests.student.answer_correct') }}
                                </span>
                            @else
                                <span class="badge text-bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>{{ __('tests.student.answer_wrong') }}
                                </span>
                            @endif
                        </div>

                        <ul class="list-unstyled mb-0 mt-3 small">
                            @foreach ($question->answers as $answer)
                                @php
                                    $isPicked = $answer->id === $pickedAnswerId;
                                    $isCorrectAnswer = (bool) $answer->is_correct;

                                    $rowClass = match (true) {
                                        $isPicked && $isCorrectAnswer => 'text-success fw-semibold',
                                        $isPicked && ! $isCorrectAnswer => 'text-danger fw-semibold',
                                        ! $isPicked && $isCorrectAnswer => 'text-success',
                                        default => 'text-muted',
                                    };

                                    $icon = match (true) {
                                        $isPicked && $isCorrectAnswer => 'bi-check-circle-fill text-success',
                                        $isPicked && ! $isCorrectAnswer => 'bi-x-circle-fill text-danger',
                                        ! $isPicked && $isCorrectAnswer => 'bi-check-circle text-success',
                                        default => 'bi-circle text-muted',
                                    };
                                @endphp
                                <li class="py-1 {{ $rowClass }}">
                                    <i class="bi {{ $icon }} me-2"></i>{{ $answer->text }}
                                    @if ($isPicked)
                                        <span class="badge text-bg-light ms-1">{{ __('tests.student.your_answer') }}</span>
                                    @endif
                                    @if ($isCorrectAnswer && ! $isPicked)
                                        <span class="badge text-bg-light ms-1">{{ __('tests.student.correct_answer') }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach

            <div class="mt-4">
                <a href="{{ route('attempts.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('tests.student.attempts_index_title') }}
                </a>
                <a href="{{ route('tests.show', $attempt->test) }}" class="btn btn-primary ms-2">
                    {{ __('tests.actions.view') }}
                </a>
            </div>
        </div>
    </div>
@endsection
