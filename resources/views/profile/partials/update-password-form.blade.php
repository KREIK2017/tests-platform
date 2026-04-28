<section>
    <header class="mb-4">
        <h2 class="h5 mb-1">{{ __('messages.profile.password_title') }}</h2>
        <p class="text-muted small mb-0">{{ __('messages.profile.password_lead') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" novalidate>
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">
                {{ __('messages.profile.current_password') }}
            </label>
            <input type="password" id="update_password_current_password" name="current_password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">
                {{ __('messages.profile.new_password') }}
            </label>
            <input type="password" id="update_password_password" name="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">
                {{ __('messages.auth_ui.password_confirmation') }}
            </label>
            <input type="password" id="update_password_password_confirmation" name="password_confirmation"
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                {{ __('messages.common.save') }}
            </button>
            @if (session('status') === 'password-updated')
                <span class="text-muted small">{{ __('messages.profile.saved') }}</span>
            @endif
        </div>
    </form>
</section>
