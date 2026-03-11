@extends('layouts.admin')

@section('title', 'Создание турнира')

@push('styles')
<style>
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-lg)}
    .form-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-lg);margin-bottom:var(--space-lg)}
    .form-row{display:flex;gap:var(--space-sm)}
    .form-row .form-group{flex:1;min-width:0}
</style>
@endpush

@section('content')
    <div class="page-title">Создание турнира</div>

    <form method="POST" action="{{ route('admin.tournaments.store') }}">
        @csrf

        <div class="form-card">
            <div class="form-group">
                <label class="form-label">Название турнира</label>
                <input type="text" name="name" class="form-input" placeholder="Например: Кубок Врачей 2025" value="{{ old('name') }}" required>
                @error('name')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Описание <span style="color:var(--text-muted);font-weight:400;">(необязательно)</span></label>
                <textarea name="description" class="form-input" placeholder="Описание турнира, правила участия..." rows="3" style="height:auto;padding-top:12px;resize:vertical;">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Дата начала</label>
                    <input type="date" name="start_date" class="form-input" value="{{ old('start_date') }}" required>
                    @error('start_date')
                        <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Дата окончания</label>
                    <input type="date" name="end_date" class="form-input" value="{{ old('end_date') }}">
                    @error('end_date')
                        <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Место проведения <span style="color:var(--text-muted);font-weight:400;">(необязательно)</span></label>
                <input type="text" name="venue" class="form-input" placeholder="Город, стадион" value="{{ old('venue') }}">
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Макс. количество команд <span style="color:var(--text-muted);font-weight:400;">(необязательно)</span></label>
                <input type="number" name="max_teams" class="form-input" placeholder="16" min="2" max="64" value="{{ old('max_teams') }}">
                @error('max_teams')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;height:48px;">Создать турнир</button>
    </form>
@endsection
