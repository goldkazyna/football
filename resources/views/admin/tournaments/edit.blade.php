@extends('layouts.admin')

@section('title', 'Редактирование турнира')

@push('styles')
<style>
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-lg)}
    .form-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-lg);margin-bottom:var(--space-lg)}
    .form-row{display:flex;gap:var(--space-sm)}
    .form-row .form-group{flex:1;min-width:0}
    .status-section{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-lg);margin-bottom:var(--space-lg)}
    .status-section-title{font-size:0.875rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-md)}
    .status-buttons{display:flex;gap:var(--space-sm);flex-wrap:wrap}
    .btn-status{height:36px;padding:0 16px;font-size:0.8125rem;font-weight:600;font-family:inherit;border:1px solid var(--border-input);border-radius:var(--radius-sm);cursor:pointer;background:transparent;color:var(--text-secondary);transition:all var(--transition-fast)}
    .btn-status:hover{background:rgba(255,255,255,0.04)}
    .btn-status.active{background:rgba(34,197,94,0.1);border-color:rgba(34,197,94,0.3);color:var(--primary)}
</style>
@endpush

@section('content')
    <div class="page-title">Редактирование турнира</div>

    {{-- Status change --}}
    <div class="status-section">
        <div class="status-section-title">Статус турнира</div>
        <div class="status-buttons">
            @foreach(['draft' => 'Черновик', 'registration' => 'Открыть регистрацию', 'closed' => 'Закрыть регистрацию'] as $status => $label)
                <form method="POST" action="{{ route('admin.tournaments.status', $tournament) }}" style="margin:0;">
                    @csrf
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button type="submit" class="btn-status {{ $tournament->status === $status ? 'active' : '' }}">{{ $label }}</button>
                </form>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('admin.tournaments.update', $tournament) }}">
        @csrf
        @method('PUT')

        <div class="form-card">
            <div class="form-group">
                <label class="form-label">Название турнира</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $tournament->name) }}" required>
                @error('name')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Описание</label>
                <textarea name="description" class="form-input" rows="3" style="height:auto;padding-top:12px;resize:vertical;">{{ old('description', $tournament->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Дата начала</label>
                    <input type="date" name="start_date" class="form-input" value="{{ old('start_date', $tournament->start_date->format('Y-m-d')) }}" required>
                    @error('start_date')
                        <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Дата окончания</label>
                    <input type="date" name="end_date" class="form-input" value="{{ old('end_date', $tournament->end_date?->format('Y-m-d')) }}">
                    @error('end_date')
                        <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Место проведения</label>
                <input type="text" name="venue" class="form-input" value="{{ old('venue', $tournament->venue) }}">
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Макс. количество команд</label>
                <input type="number" name="max_teams" class="form-input" min="2" max="64" value="{{ old('max_teams', $tournament->max_teams) }}">
                @error('max_teams')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;height:48px;">Сохранить</button>
    </form>
@endsection
