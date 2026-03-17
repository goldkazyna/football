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
    Alpine.data('loginForm', () => ({
        digits: '',
        password: '',
        step: 'phone',
        loading: false,
        errorMsg: '',

        init() {
            let initial = '{{ old("phone", "") }}'.replace(/[^0-9]/g, '');
            if (initial.length === 11 && (initial[0] === '7' || initial[0] === '8')) initial = initial.substring(1);
            this.digits = initial.substring(0, 10);

            @if($errors->has('password'))
                this.step = 'password';
            @endif
        },

        get display() {
            let d = this.digits;
            if (d.length === 0) return '';
            let out = '(' + d.substring(0, 3);
            if (d.length >= 3) out += ') ' + d.substring(3, 6);
            if (d.length >= 6) out += '-' + d.substring(6, 8);
            if (d.length >= 8) out += '-' + d.substring(8, 10);
            return out;
        },

        get phone() {
            return '+7' + this.digits;
        },

        get phoneComplete() {
            return this.digits.length === 10;
        },

        onInput(e) {
            let raw = e.target.value.replace(/[^0-9]/g, '');
            if (raw.length >= 11 && (raw[0] === '7' || raw[0] === '8')) raw = raw.substring(1);
            this.digits = raw.substring(0, 10);
            if (this.step !== 'phone') {
                this.step = 'phone';
                this.password = '';
                this.errorMsg = '';
            }
            this.$nextTick(() => { e.target.value = this.display; });
        },

        onBackspace(e) {
            if (this.digits.length > 0) {
                this.digits = this.digits.slice(0, -1);
                if (this.step !== 'phone') {
                    this.step = 'phone';
                    this.password = '';
                    this.errorMsg = '';
                }
                e.target.value = this.display;
            }
        },

        async checkPhone() {
            if (!this.phoneComplete) return;

            this.loading = true;
            this.errorMsg = '';

            try {
                const res = await fetch('{{ route("login.checkPhone") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ phone: this.phone })
                });

                const data = await res.json();

                if (data.status === 'password') {
                    this.step = 'password';
                    this.$nextTick(() => this.$refs.passwordInput?.focus());
                } else if (data.status === 'register' || data.status === 'login') {
                    this.$refs.form.submit();
                } else {
                    this.errorMsg = 'Ваш номер не найден. Обратитесь к администратору.';
                }
            } catch (e) {
                this.errorMsg = 'Ошибка соединения. Попробуйте ещё раз.';
            }

            this.loading = false;
        },

        submitForm(e) {
            if (this.step === 'phone') {
                e.preventDefault();
                this.checkPhone();
            }
        }
    }));
});
</script>
@endpush

@section('content')
    <div class="login-header animate-in animate-in-delay-2">
        <h1>Добро пожаловать</h1>
        <p>Вход и регистрация на турнир</p>
    </div>

    <form x-data="loginForm" x-ref="form" action="{{ route('login.phone') }}" method="POST" @submit="submitForm($event)" class="animate-in animate-in-delay-3">
        @csrf
        <input type="hidden" name="phone" :value="phone">

        <div class="form-group">
            <label class="form-label">Номер телефона</label>
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
            <div x-show="errorMsg" x-text="errorMsg" x-cloak style="color:var(--error);font-size:0.75rem;margin-top:4px;"></div>
        </div>

        <div x-show="step === 'password'" x-transition x-cloak class="form-group">
            <label class="form-label">Пароль</label>
            <input
                type="password"
                name="password"
                x-ref="passwordInput"
                x-model="password"
                class="form-input @error('password') is-invalid @enderror"
                placeholder="Введите пароль"
            >
            @error('password')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-actions">
            <button type="submit" class="btn btn-primary" :disabled="loading || !phoneComplete">
                <template x-if="loading">
                    <span>Проверяем...</span>
                </template>
                <template x-if="!loading && step === 'phone'">
                    <span style="display:inline-flex;align-items:center;gap:8px;">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                        Продолжить
                    </span>
                </template>
                <template x-if="!loading && step === 'password'">
                    <span>Войти</span>
                </template>
            </button>
        </div>
    </form>

    <div class="login-footer animate-in animate-in-delay-4">
        <p>
            Продолжая, вы соглашаетесь с<br>
            <a href="{{ route('legal.oferta') }}">Договором-офертой</a> и <a href="{{ route('legal.politika-konfidencialnosti') }}">Политикой конфиденциальности</a>
        </p>
        <p style="margin-top: 6px;">
            <a href="{{ route('legal.opisanie-uslug') }}">Описание услуг</a> &middot; <a href="{{ route('legal.oplata-i-vozvrat') }}">Оплата и возврат</a>
        </p>
    </div>
@endsection
