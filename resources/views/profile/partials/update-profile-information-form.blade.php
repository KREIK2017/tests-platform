<section>
    <header class="mb-4">
        <h2 class="h5 mb-1">{{ __('messages.profile.info_title') }}</h2>
        <p class="text-muted small mb-0">{{ __('messages.profile.info_lead') }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" novalidate>
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('messages.auth_ui.name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('messages.auth_ui.email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="form-text mt-2">
                    {{ __('messages.profile.unverified') }}
                    <button form="send-verification" type="submit" class="btn btn-link btn-sm p-0 align-baseline">
                        {{ __('messages.profile.resend_link') }}
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2 py-2 small">
                        {{ __('messages.profile.verification_resent') }}
                    </div>
                @endif
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                {{ __('messages.common.save') }}
            </button>
            @if (session('status') === 'profile-updated')
                <span class="text-muted small">{{ __('messages.profile.saved') }}</span>
            @endif
        </div>
    </form>
</section>
