<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand brand-mark d-flex align-items-center gap-2" href="{{ route('home') }}">
                <i class="bi bi-mortarboard-fill text-primary"></i>
                {{ __('messages.app.name') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            {{ __('messages.nav.home') }}
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                               href="{{ route('dashboard') }}">
                                {{ __('messages.nav.dashboard') }}
                            </a>
                        </li>
                        @if (auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.tests.*') ? 'active' : '' }}"
                                   href="{{ route('admin.tests.index') }}">
                                    {{ __('messages.nav.admin_tests') }}
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <div class="btn-group locale-switch" role="group" aria-label="{{ __('messages.common.language') }}">
                        @foreach (['uk' => 'UA', 'en' => 'EN'] as $loc => $label)
                            <a href="{{ route('locale.switch', $loc) }}"
                               class="btn btn-outline-secondary {{ app()->getLocale() === $loc ? 'active' : '' }}"
                               aria-current="{{ app()->getLocale() === $loc ? 'true' : 'false' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-gear me-2"></i>{{ __('messages.nav.profile') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('messages.nav.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            {{ __('messages.nav.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            {{ __('messages.nav.register') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="app-main">
        <div class="container">
            @foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $variant)
                @if (session($key))
                    <div class="alert alert-{{ $variant }} alert-dismissible fade show" role="alert">
                        {{ session($key) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endforeach

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container text-center text-muted small">
            {{ __('messages.footer.copyright', ['year' => now()->year, 'name' => __('messages.app.name')]) }}
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
