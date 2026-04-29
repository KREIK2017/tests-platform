@extends('layouts.app')

@section('title', $attempt->test->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="mb-4">
                <h1 class="h3 mb-1">{{ $attempt->test->title }}</h1>
                <p class="text-muted mb-0">{{ __('tests.student.take_intro') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <p class="mb-1"><strong>{{ __('tests.student.unanswered_warning') }}</strong></p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('attempts.finish', $attempt) }}" id="takeForm">
                @csrf

                @foreach ($attempt->test->questions as $i => $question)
                    <div class="card shadow-sm border-0 mb-3" id="q-{{ $question->id }}">
                        <div class="card-body">
                            <p class="text-muted small mb-1">
                                {{ __('tests.student.taking_progress', ['index' => $i + 1, 'total' => $attempt->test->questions->count()]) }}
                            </p>
                            <h2 class="h5">{{ $question->text }}</h2>

                            @error("answers.{$question->id}")
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror

                            <div class="mt-3">
                                @foreach ($question->answers as $answer)
                                    @php($inputId = "q{$question->id}-a{$answer->id}")
                                    <div class="form-check py-1">
                                        <input type="radio"
                                               id="{{ $inputId }}"
                                               name="answers[{{ $question->id }}]"
                                               value="{{ $answer->id }}"
                                               class="form-check-input"
                                               {{ (int) old("answers.{$question->id}") === $answer->id ? 'checked' : '' }}
                                               required>
                                        <label for="{{ $inputId }}" class="form-check-label">
                                            {{ $answer->text }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check2-circle me-1"></i>{{ __('tests.student.finish_button') }}
                    </button>
                    <a href="{{ route('tests.show', $attempt->test) }}" class="btn btn-outline-secondary btn-lg">
                        {{ __('messages.common.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
