@extends('layouts.app')

@section('title', __('tests.admin.question_edit_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('admin.tests.show', $question->test) }}" class="text-decoration-none small text-muted">
                        <i class="bi bi-arrow-left me-1"></i>{{ $question->test->title }}
                    </a>
                    <h1 class="h3 mb-0 mt-1">{{ __('tests.admin.question_edit_title') }}</h1>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.questions.update', $question) }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="text" class="form-label">
                                {{ __('tests.questions.fields.text') }} <span class="text-danger">*</span>
                            </label>
                            <textarea id="text" name="text" rows="3"
                                      class="form-control @error('text') is-invalid @enderror"
                                      required autofocus>{{ old('text', $question->text) }}</textarea>
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="order" class="form-label">{{ __('tests.questions.fields.order') }}</label>
                            <input type="number" id="order" name="order" min="0"
                                   value="{{ old('order', $question->order) }}"
                                   class="form-control form-control-sm @error('order') is-invalid @enderror"
                                   style="max-width: 8rem;">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('messages.common.save') }}
                            </button>
                            <a href="{{ route('admin.tests.show', $question->test) }}"
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
