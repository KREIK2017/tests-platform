@extends('layouts.app')

@section('title', __('tests.admin.answer_create_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('admin.tests.show', $question->test_id) }}" class="text-decoration-none small text-muted">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('messages.common.back') }}
                    </a>
                    <h1 class="h4 mb-0 mt-1">{{ __('tests.admin.answer_create_title') }}</h1>
                    <p class="text-muted small mb-0">{{ $question->text }}</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.questions.answers.store', $question) }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="text" class="form-label">
                                {{ __('tests.answers.fields.text') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="text" name="text" value="{{ old('text') }}"
                                   class="form-control @error('text') is-invalid @enderror"
                                   placeholder="{{ __('tests.admin.answer_text_placeholder') }}"
                                   required autofocus maxlength="255">
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input type="hidden" name="is_correct" value="0">
                            <input type="checkbox" id="is_correct" name="is_correct" value="1"
                                   class="form-check-input" {{ old('is_correct') ? 'checked' : '' }}>
                            <label for="is_correct" class="form-check-label">
                                {{ __('tests.admin.is_correct_label') }}
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('messages.common.create') }}
                            </button>
                            <a href="{{ route('admin.tests.show', $question->test_id) }}"
                               class="btn btn-outline-secondary">
                                {{ __('messages.common.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
