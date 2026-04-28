@extends('layouts.app')

@section('title', __('messages.app.name') . ' — ' . __('messages.app.tagline'))

@section('content')
    <section class="hero text-center">
        <h1 class="display-4 fw-bold">{{ __('messages.welcome.title') }}</h1>
        <p class="lead text-muted col-lg-8 mx-auto mt-3">{{ __('messages.welcome.subtitle') }}</p>

        <div class="d-flex justify-content-center gap-3 mt-4">
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-person-plus me-2"></i>{{ __('messages.welcome.cta_register') }}
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('messages.welcome.cta_login') }}
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-speedometer2 me-2"></i>{{ __('messages.nav.dashboard') }}
                </a>
            @endguest
        </div>
    </section>

    <section class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="display-6 text-primary mb-2"><i class="bi bi-gear-fill"></i></div>
                    <h5 class="card-title">{{ __('messages.welcome.features.admin_title') }}</h5>
                    <p class="card-text text-muted">{{ __('messages.welcome.features.admin_text') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="display-6 text-primary mb-2"><i class="bi bi-mortarboard-fill"></i></div>
                    <h5 class="card-title">{{ __('messages.welcome.features.student_title') }}</h5>
                    <p class="card-text text-muted">{{ __('messages.welcome.features.student_text') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="display-6 text-primary mb-2"><i class="bi bi-translate"></i></div>
                    <h5 class="card-title">{{ __('messages.welcome.features.i18n_title') }}</h5>
                    <p class="card-text text-muted">{{ __('messages.welcome.features.i18n_text') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
