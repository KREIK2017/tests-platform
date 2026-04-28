@extends('layouts.app')

@section('title', __('messages.auth_ui.login_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4 text-center">{{ __('messages.auth_ui.login_title') }}</h1>

                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('messages.auth_ui.email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autofocus autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('messages.auth_ui.password') }}</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" id="remember_me" name="remember" class="form-check-input">
                            <label for="remember_me" class="form-check-label">
                                {{ __('messages.auth_ui.remember_me') }}
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('messages.nav.login') }}
                        </button>

                        <div class="d-flex justify-content-between mt-3 small">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="link-secondary">
                                    {{ __('messages.auth_ui.forgot_password') }}
                                </a>
                            @endif
                            <a href="{{ route('register') }}" class="link-secondary">
                                {{ __('messages.auth_ui.no_account') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
