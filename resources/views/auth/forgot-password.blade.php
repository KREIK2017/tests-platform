@extends('layouts.app')

@section('title', __('messages.auth_ui.forgot_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3 text-center">{{ __('messages.auth_ui.forgot_title') }}</h1>

                    <p class="text-muted">{{ __('messages.auth_ui.forgot_intro') }}</p>

                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('messages.auth_ui.email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('messages.auth_ui.send_reset_link') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
