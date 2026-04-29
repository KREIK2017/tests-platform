@extends('layouts.app')

@php($isAdminView = auth()->user()->isAdmin())

@section('title', $isAdminView ? __('tests.student.admin_attempts_title') : __('tests.student.attempts_index_title'))

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1">
            {{ $isAdminView ? __('tests.student.admin_attempts_title') : __('tests.student.attempts_index_title') }}
        </h1>
        <p class="text-muted mb-0">
            {{ $isAdminView ? __('tests.student.admin_attempts_lead') : __('tests.student.attempts_index_lead') }}
        </p>
    </div>

    @if ($attempts->isEmpty())
        <div class="alert alert-light border text-center py-5">
            <i class="bi bi-inbox display-5 d-block mb-2 text-muted"></i>
            <p class="mb-0">
                {{ $isAdminView ? __('tests.messages.no_admin_attempts') : __('tests.messages.no_attempts') }}
            </p>
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('tests.attempts.fields.test') }}</th>
                            @if ($isAdminView)
                                <th scope="col">{{ __('tests.attempts.fields.student') }}</th>
                            @endif
                            <th scope="col">{{ __('tests.attempts.fields.result') }}</th>
                            <th scope="col">{{ __('tests.attempts.fields.completed_at') }}</th>
                            <th scope="col" class="text-end">{{ __('messages.common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attempts as $attempt)
                            @php($isCompleted = $attempt->completed_at !== null)
                            @php($percent = $attempt->total_questions > 0 ? (int) round(100 * $attempt->score / $attempt->total_questions) : 0)
                            @php($resultClass = ! $isCompleted ? 'text-bg-secondary' : ($percent >= 80 ? 'text-bg-success' : ($percent >= 50 ? 'text-bg-warning' : 'text-bg-danger')))
                            <tr>
                                <td>{{ $attempt->test->title }}</td>
                                @if ($isAdminView)
                                    <td>{{ $attempt->user->name }}</td>
                                @endif
                                <td>
                                    @if ($isCompleted)
                                        <span class="badge {{ $resultClass }}">
                                            {{ __('tests.attempts.score_format', [
                                                'score' => $attempt->score,
                                                'total' => $attempt->total_questions,
                                            ]) }}
                                        </span>
                                        <small class="text-muted ms-1">{{ $percent }}%</small>
                                    @else
                                        <span class="badge {{ $resultClass }}">
                                            {{ __('tests.student.attempt_status_in_progress') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $attempt->completed_at?->isoFormat('LLL') ?? '—' }}
                                </td>
                                <td class="text-end">
                                    @if ($isCompleted)
                                        <a href="{{ route('attempts.show', $attempt) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>{{ __('tests.actions.view') }}
                                        </a>
                                    @else
                                        <a href="{{ route('attempts.take', $attempt) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-play-circle me-1"></i>{{ __('messages.common.confirm') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $attempts->links() }}
        </div>
    @endif
@endsection
