@extends('layouts.guest')

@section('title', 'Регистрация — Пароль')

@push('styles')
<style>
    .steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: var(--space-xl);
    }

    .step-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--border-input);
        transition: all var(--transition-base);
    }

    .step-dot.active {
        width: 24px;
        border-radius: 4px;
        background: var(--primary);
    }

    .step-dot.done {
        background: var(--primary);
    }

    .register-header {
        text-align: center;
        margin-bottom: var(--space-xl);
    }

    .register-header h1 {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }

    .register-header p {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .nav-buttons {
        display: flex;
        gap: var(--space-sm);
        margin-top: var(--space-lg);
    }

    .btn-back {
        width: 48px;
        min-width: 48px;
        height: 48px;
        flex-shrink: 0;
        background: transparent;
        border: 1px solid var(--border-input);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.04);
    }

    .btn-back svg {
        width: 20px;
        height: 20px;
    }

    .password-hint {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 6px;
        transition: color 0.2s;
    }
    .password-hint svg { width: 14px; height: 14px; flex-shrink: 0; }
    .password-hint.valid { color: var(--success, #22c55e); }
    .password-hint.invalid { color: var(--error, #ef4444); }

    .password-match-status {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        margin-top: 6px;
        min-height: 20px;
        transition: color 0.2s;
    }
    .password-match-status svg { width: 14px; height: 14px; flex-shrink: 0; }
    .password-match-status.match { color: var(--success, #22c55e); }
    .password-match-status.mismatch { color: var(--error, #ef4444); }
    .password-match-status.empty { color: var(--text-muted); }
</style>
@endpush

@section('content')
    <!-- Steps -->
    <div class="steps animate-in animate-in-delay-1">
        <div class="step-dot done"></div>
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
    </div>

    <!-- Header -->
    <div class="register-header animate-in animate-in-delay-1">
        <h1>Придумайте пароль</h1>
        <p>Минимум 6 символов</p>
    </div>

    <!-- Form -->
    <form class="animate-in animate-in-delay-3" method="POST" action="{{ route('register.process', 'password') }}"
          x-data="{
              password: '',
              confirm: '',
              get lengthOk() { return this.password.length >= 6 },
              get matches() { return this.password !== '' && this.confirm !== '' && this.password === this.confirm },
              get mismatch() { return this.confirm !== '' && this.password !== this.confirm }
          }">
        @csrf

        <div class="form-group">
            <label class="form-label">Пароль</label>
            <input
                type="password"
                name="password"
                class="form-input @error('password') is-invalid @enderror"
                placeholder="Введите пароль"
                x-model="password"
                required
            >
            <div class="password-hint" :class="{ 'valid': lengthOk, 'invalid': password.length > 0 && !lengthOk }">
                <template x-if="lengthOk">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </template>
                <template x-if="!lengthOk">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </template>
                <span>Минимум 6 символов</span>
            </div>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Повторите пароль</label>
            <input
                type="password"
                name="password_confirmation"
                class="form-input"
                :class="{ 'is-invalid': mismatch }"
                placeholder="Повторите пароль"
                x-model="confirm"
                required
            >
            <div class="password-match-status"
                 :class="{ 'match': matches, 'mismatch': mismatch, 'empty': confirm === '' }">
                <template x-if="matches">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </template>
                <template x-if="mismatch">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </template>
                <span x-show="matches">Пароли совпадают</span>
                <span x-show="mismatch">Пароли не совпадают</span>
            </div>
        </div>

        <div class="nav-buttons">
            <a href="{{ route('register.step', 'phone') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <button type="submit" class="btn btn-primary" style="flex:1;" :disabled="!lengthOk || !matches">Далее</button>
        </div>
    </form>

    <div class="auth-footer animate-in animate-in-delay-4">
        Шаг 2 из 6
    </div>
@endsection
