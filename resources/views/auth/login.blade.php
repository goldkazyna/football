@extends('layouts.guest')

@section('title', 'Вход')

@push('styles')
<style>
    .login-header {
        text-align: center;
        margin-bottom: var(--space-xl);
    }
    .login-header h1 {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }
    .login-header p {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    .login-actions {
        display: flex;
        flex-direction: column;
        gap: var(--space-md);
    }
    .login-footer {
        margin-top: var(--space-lg);
        text-align: center;
    }
    .login-footer p {
        font-size: 0.75rem;
        color: var(--text-muted);
        line-height: 1.7;
    }
    .login-footer a {
        color: var(--accent-blue);
        font-weight: 500;
    }
    .phone-masked-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .phone-masked-wrapper .phone-prefix-label {
        position: absolute;
        left: 16px;
        font-size: 0.9375rem;
        color: var(--text-primary);
        pointer-events: none;
        font-weight: 500;
    }
    .phone-masked-wrapper .form-input {
        padding-left: 46px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('phoneMask', (initial) => ({
        digits: '',
        init() {
            let d = (initial || '').replace(/[^0-9]/g, '');
            // Strip country code if full number
            if (d.length === 11 && (d[0] === '7' || d[0] === '8')) d = d.substring(1);
            this.digits = d.substring(0, 10);
        },
        get display() {
            return this.formatDisplay(this.digits);
        },
        get phone() {
            return '+7' + this.digits;
        },
        formatDisplay(d) {
            if (d.length === 0) return '';
            let out = '(' + d.substring(0, 3);
            if (d.length >= 3) out += ') ' + d.substring(3, 6);
            if (d.length >= 6) out += '-' + d.substring(6, 8);
            if (d.length >= 8) out += '-' + d.substring(8, 10);
            return out;
        },
        onInput(e) {
            let raw = e.target.value.replace(/[^0-9]/g, '');
            // If pasted full number with country code
            if (raw.length >= 11 && (raw[0] === '7' || raw[0] === '8')) raw = raw.substring(1);
            this.digits = raw.substring(0, 10);
            this.$nextTick(() => { e.target.value = this.display; });
        },
        onBackspace(e) {
            if (this.digits.length > 0) {
                this.digits = this.digits.slice(0, -1);
                e.target.value = this.display;
            }
        }
    }));
});
</script>
@endpush

@section('content')
    <div class="login-header animate-in animate-in-delay-2">
        <h1>Добро пожаловать</h1>
        <p>Регистрация на футбольный турнир</p>
    </div>

    <form action="{{ route('login.phone') }}" method="POST" class="animate-in animate-in-delay-3">
        @csrf
        <div class="form-group" x-data="phoneMask('{{ old('phone', '') }}')">
            <label class="form-label">Номер телефона</label>
            <input type="hidden" name="phone" :value="phone">
            <div class="phone-masked-wrapper">
                <span class="phone-prefix-label">+7</span>
                <input
                    type="tel"
                    class="form-input"
                    placeholder="(700) 123-45-67"
                    inputmode="numeric"
                    autocomplete="tel"
                    :value="display"
                    @input="onInput($event)"
                    @keydown.backspace.prevent="onBackspace($event)"
                    required
                >
            </div>
            @error('phone')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-actions">
            <button type="submit" class="btn btn-primary">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                </svg>
                Войти по номеру
            </button>

            <div class="divider">
                <span>или войдите через</span>
            </div>

            <button type="button" class="btn btn-telegram" onclick="alert('Telegram-вход будет доступен позже')">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
                Войти через Telegram
            </button>
        </div>
    </form>

    <div class="login-footer animate-in animate-in-delay-4">
        <p>
            Продолжая, вы соглашаетесь с<br>
            <a href="#">Условиями использования</a> и <a href="#">Политикой конфиденциальности</a>
        </p>
    </div>
@endsection
