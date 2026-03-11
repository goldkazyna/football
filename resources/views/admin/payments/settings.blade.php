@extends('layouts.admin')

@section('title', 'Настройки платформы')

@push('styles')
<style>
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-lg)}
    .settings-section{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-md)}
    .settings-section-title{font-size:0.8125rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-md);display:flex;align-items:center;gap:var(--space-sm)}
    .settings-section-title svg{width:16px;height:16px;color:var(--accent-blue)}
    .save-btn-wrap{margin-top:var(--space-lg)}
</style>
@endpush

@section('content')
    <div class="page-title">Настройки платформы</div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        {{-- Membership Fee --}}
        <div class="settings-section">
            <div class="settings-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Членский взнос
            </div>
            <div class="form-group">
                <label class="form-label">Сумма</label>
                <input type="number" name="membership_fee" class="form-input" value="{{ old('membership_fee', $settings['membership_fee'] ?? 15000) }}">
                @error('membership_fee')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Валюта</label>
                <input type="text" class="form-input" value="&#8376;" disabled style="opacity:0.6;">
            </div>
        </div>

        <div class="save-btn-wrap">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
@endsection
