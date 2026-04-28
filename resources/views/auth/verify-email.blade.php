@extends('layouts.app')

@section('title', __('messages.auth_ui.verify_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3 text-center">{{ __('messages.auth_ui.verify_title') }}</h1>

                    <p class="text-muted">{{ __('messages.auth_ui.verify_intro') }}</p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success">
                            {{ __('messages.auth_ui.verify_resent') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-envelope-arrow-up me-1"></i>
                                {{ __('messages.auth_ui.verify_resend') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link link-secondary">
                                {{ __('messages.nav.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
