@extends('layouts.app')

@section('title', __('messages.auth_ui.register_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4 text-center">{{ __('messages.auth_ui.register_title') }}</h1>

                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('messages.auth_ui.name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required autofocus autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('messages.auth_ui.email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('messages.auth_ui.password') }}</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                {{ __('messages.auth_ui.password_confirmation') }}
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" required autocomplete="new-password">
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label">{{ __('messages.auth_ui.role') }}</label>
                            <select id="role" name="role" required
                                    class="form-select @error('role') is-invalid @enderror">
                                <option value="student" @selected(old('role', 'student') === 'student')>
                                    {{ __('messages.auth_ui.role_student') }}
                                </option>
                                <option value="admin" @selected(old('role') === 'admin')>
                                    {{ __('messages.auth_ui.role_admin') }}
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('messages.nav.register') }}
                        </button>

                        <div class="text-center mt-3 small">
                            <a href="{{ route('login') }}" class="link-secondary">
                                {{ __('messages.auth_ui.already_registered') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
