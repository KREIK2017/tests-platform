<section>
    <header class="mb-3">
        <h2 class="h5 mb-1 text-danger">{{ __('messages.profile.delete_title') }}</h2>
        <p class="text-muted small mb-0">{{ __('messages.profile.delete_lead') }}</p>
    </header>

    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
        <i class="bi bi-trash me-1"></i>{{ __('messages.profile.delete_button') }}
    </button>

    <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-labelledby="confirmUserDeletionTitle"
         aria-hidden="true" @if ($errors->userDeletion->isNotEmpty()) data-show-on-load="true" @endif>
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUserDeletionTitle">
                        {{ __('messages.profile.delete_modal_title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="{{ __('messages.common.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">{{ __('messages.profile.delete_modal_lead') }}</p>

                    <div>
                        <label for="password" class="visually-hidden">{{ __('messages.auth_ui.password') }}</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="{{ __('messages.auth_ui.password') }}">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('messages.common.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        {{ __('messages.profile.delete_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('confirmUserDeletion')).show();
            });
        </script>
    @endif
</section>
