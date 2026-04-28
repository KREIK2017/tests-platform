@extends('layouts.app')

@section('title', __('tests.admin.question_create_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('admin.tests.show', $test) }}" class="text-decoration-none small text-muted">
                        <i class="bi bi-arrow-left me-1"></i>{{ $test->title }}
                    </a>
                    <h1 class="h3 mb-0 mt-1">{{ __('tests.admin.question_create_title') }}</h1>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.tests.questions.store', $test) }}" novalidate>
                @csrf

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="text" class="form-label">
                                {{ __('tests.questions.fields.text') }} <span class="text-danger">*</span>
                            </label>
                            <textarea id="text" name="text" rows="3"
                                      class="form-control @error('text') is-invalid @enderror"
                                      placeholder="{{ __('tests.admin.question_text_placeholder') }}"
                                      required autofocus>{{ old('text') }}</textarea>
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="order" class="form-label">{{ __('tests.admin.order_optional') }}</label>
                            <input type="number" id="order" name="order" min="0" value="{{ old('order') }}"
                                   class="form-control form-control-sm @error('order') is-invalid @enderror"
                                   style="max-width: 8rem;">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">
                            {{ __('tests.admin.answers_block') }}
                            <small class="text-muted">— {{ __('tests.answers.select_correct') }}</small>
                        </h2>

                        @error('answers')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @error('correct_answer')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        @for ($i = 0; $i < 4; $i++)
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-auto">
                                    <div class="form-check m-0">
                                        <input type="radio" id="correct_{{ $i }}" name="correct_answer" value="{{ $i }}"
                                               class="form-check-input"
                                               {{ (int) old('correct_answer', 0) === $i ? 'checked' : '' }}>
                                        <label for="correct_{{ $i }}" class="form-check-label small text-muted">
                                            #{{ $i + 1 }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <input type="text" name="answers[{{ $i }}][text]"
                                           value="{{ old("answers.$i.text") }}"
                                           class="form-control @error("answers.$i.text") is-invalid @enderror"
                                           placeholder="{{ __('tests.admin.answer_text_placeholder') }} #{{ $i + 1 }}"
                                           required maxlength="255">
                                    @error("answers.$i.text")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('messages.common.create') }}
                    </button>
                    <a href="{{ route('admin.tests.show', $test) }}" class="btn btn-outline-secondary">
                        {{ __('messages.common.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
