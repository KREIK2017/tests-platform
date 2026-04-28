@extends('layouts.app')

@section('title', __('tests.admin.create_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ __('tests.admin.create_title') }}</h1>
                <a href="{{ route('admin.tests.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('messages.common.back') }}
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.tests.store') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('tests.fields.title') }} <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="{{ __('tests.admin.placeholder_title') }}"
                                   required autofocus maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('tests.fields.description') }}</label>
                            <textarea id="description" name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="{{ __('tests.admin.placeholder_description') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" id="is_published" name="is_published" value="1"
                                   class="form-check-input" {{ old('is_published') ? 'checked' : '' }}>
                            <label for="is_published" class="form-check-label">
                                {{ __('tests.fields.is_published') }}
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('messages.common.create') }}
                            </button>
                            <a href="{{ route('admin.tests.index') }}" class="btn btn-outline-secondary">
                                {{ __('messages.common.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
