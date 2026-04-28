@extends('layouts.app')

@section('title', __('messages.auth_ui.confirm_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3 text-center">{{ __('messages.auth_ui.confirm_title') }}</h1>

                    <p class="text-muted">{{ __('messages.auth_ui.confirm_intro') }}</p>

                    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('messages.auth_ui.password') }}</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('messages.common.confirm') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
