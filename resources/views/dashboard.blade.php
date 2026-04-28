@extends('layouts.app')

@section('title', __('messages.nav.dashboard'))

@section('content')
    @php($user = auth()->user())

    <div class="mb-4">
        <h1 class="h3 mb-1">
            {{ __('messages.dashboard.greeting', ['name' => $user->name]) }}
        </h1>
        @if ($user->isAdmin())
            <p class="text-muted mb-0">{{ __('messages.dashboard.admin_lead') }}</p>
        @else
            <p class="text-muted mb-0">{{ __('messages.dashboard.student_lead') }}</p>
        @endif
    </div>

    @if ($user->isAdmin())
        <h2 class="h5 text-uppercase text-muted mb-3">{{ __('messages.dashboard.admin_title') }}</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card test-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="display-6 text-primary mb-2"><i class="bi bi-plus-circle"></i></div>
                        <h5 class="card-title">{{ __('messages.dashboard.cards.create_test') }}</h5>
                        <p class="card-text text-muted">{{ __('messages.dashboard.cards.create_test_lead') }}</p>
                        <span class="btn btn-primary disabled" aria-disabled="true">
                            {{ __('messages.common.create') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card test-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="display-6 text-primary mb-2"><i class="bi bi-list-check"></i></div>
                        <h5 class="card-title">{{ __('messages.dashboard.cards.all_tests') }}</h5>
                        <p class="card-text text-muted">{{ __('messages.dashboard.cards.all_tests_lead') }}</p>
                        <span class="btn btn-outline-primary disabled" aria-disabled="true">
                            {{ __('messages.nav.admin_tests') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card test-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="display-6 text-primary mb-2"><i class="bi bi-bar-chart"></i></div>
                        <h5 class="card-title">{{ __('messages.dashboard.cards.attempts') }}</h5>
                        <p class="card-text text-muted">{{ __('messages.dashboard.cards.attempts_lead') }}</p>
                        <span class="btn btn-outline-primary disabled" aria-disabled="true">
                            {{ __('messages.nav.admin_attempts') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <h2 class="h5 text-uppercase text-muted mb-3">{{ __('messages.dashboard.student_title') }}</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card test-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="display-6 text-primary mb-2"><i class="bi bi-journal-text"></i></div>
                        <h5 class="card-title">{{ __('messages.dashboard.cards.available_tests') }}</h5>
                        <p class="card-text text-muted">{{ __('messages.dashboard.cards.available_tests_lead') }}</p>
                        <span class="btn btn-primary disabled" aria-disabled="true">
                            {{ __('messages.nav.tests') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card test-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="display-6 text-primary mb-2"><i class="bi bi-clock-history"></i></div>
                        <h5 class="card-title">{{ __('messages.dashboard.cards.my_attempts') }}</h5>
                        <p class="card-text text-muted">{{ __('messages.dashboard.cards.my_attempts_lead') }}</p>
                        <span class="btn btn-outline-primary disabled" aria-disabled="true">
                            {{ __('messages.nav.my_attempts') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
